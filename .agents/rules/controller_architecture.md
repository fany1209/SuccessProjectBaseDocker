---
trigger: always_on
---



# Patrón de Arquitectura Desacoplada y Estándares de Seguridad para Controladores

Este documento define la arquitectura obligatoria de 4 capas y los estándares de seguridad, concurrencia y gestión de archivos que deben aplicarse a todos los componentes refactorizados en este proyecto.

---

## 🏛️ Estructura de Capas y Estándares

1. **Controlador (`App\Http\Controllers\...`)**:
* Inyecta únicamente `UtilResponse` y el Repositorio de la entidad vía constructor.
* Responsabilidad única: orquestar el flujo HTTP, delegando validación, persistencia, serialización y respuestas.
* **Resiliencia y Observabilidad:**
* Envolver las operaciones en bloques `try / catch (\Throwable $e)`.
* Registrar errores inesperados con `Log::error(...)` incluyendo contexto (`action`, `user_id`, `payload`, `exception`).
* Responder con `$this->utilResponse->errorResponse('Mensaje seguro para usuario', 500)` sin fugar detalles internos ni trazas de base de datos.




2. **FormRequest (`App\Http\Requests\<Entity>\<Entity>Request`)**:
* Centraliza la validación de entrada (`rules()`) para `POST` (creación) y `PUT`/`PATCH` (actualización ignorando el ID actual para reglas `unique`).
* `messages()`: Mensajes legibles y profesionales en español.
* **Sanitización Defensiva (Anti Stored-XSS):**
* Implementar `prepareForValidation()` para aplicar `strip_tags(trim(...))` sobre todas las entradas de texto antes de ser validadas y persistidas.


* **Autorización:**
* Por defecto `authorize()` retorna `true` a menos que se especifiquen explícitamente políticas o permisos específicos.




3. **Repositorio (`App\Http\Repositories\<Entity>\<Entity>Repository`)**:
* Inyecta el modelo Eloquent en el constructor.
* Métodos estándar: `all()`, `find($id)`, `create(array $data)`, `update($id, array $data)`, `delete($id)` y métodos de consulta de relaciones (e.g. `hasEquipment($id)`).
* **Transaccionalidad ACID y Concurrencia:**
* Envolver todas las mutaciones (`create`, `update`, `delete`) dentro de `DB::transaction()`.
* En `update()` y `delete()`, usar **bloqueo pesimista (`lockForUpdate()`)** para prevenir condiciones de carrera TOCTOU (*Time-of-Check to Time-of-Use*).
* Usar métodos óptimos de existencia en relaciones (`exists()` en lugar de `count() > 0`).


* **Manejo Físico de Archivos:**
* Si el método almacena, sustituye o borra archivos, debe hacerlo aplicando la resolución física estandarizada hacia `public_html` (ver punto 6).
* Limpiar archivos anteriores/huérfanos en disco al actualizar (`unlink`) o borrar registros.




4. **Resource (`App\Http\Resources\<Entity>\<Entity>Resource`)**:
* Extiende `JsonResource`.
* Transforma atributos de forma tipada, exponiendo únicamente los campos necesarios y formateando fechas con `d-m-Y H:i:s`.


5. **UtilResponse (`App\Traits\UtilResponse`)**:
* Estandariza respuestas JSON garantizando compatibilidad con Blade/jQuery (`success`) y APIs (`flag`):
* `successResponse($data, $message, $code = 200)` -> `{"success": true, "flag": true, "code": $code, "message": $message, "data": $data}`
* `errorResponse($message, $code = 404)` -> `{"success": false, "flag": false, "code": $code, "message": $message, "data": []}`




6. **Gestión de Archivos y Rutas Públicas Desacopladas (`public_html`)**:
* **Contexto de Infraestructura:** El código fuente reside en una carpeta hermana (`test_migration/`) mientras que los activos públicos y servibles por web residen en `public_html/`.
* **Prohibición de `public_path()`:** Queda **estrictamente prohibido** usar el helper nativo `public_path()` para almacenar, consultar existencia o eliminar archivos físicos en disco, ya que apunta erróneamente a `test_migration/public/`.
* **Resolución Obligatoria con `base_path()`:**
* Toda ruta física a disco debe resolverse manualmente saliendo del directorio base de Laravel hacia `public_html`:
```php
protected function getPublicHtmlPath(string $subpath = ''): string
{
    $base = base_path('../public_html');
    return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
}

```




* **Regla de Persistencia en Base de Datos:**
* En base de datos **únicamente** se almacena la ruta relativa web normalizada (ejemplo: `uploads/cxp/payments/archivo.pdf`), omitiendo prefijos como `public_html/` o `../` para garantizar la compatibilidad con helpers front-end (`asset(...)` o URLs relativas `/uploads/...`).


* **Operaciones de Escritura y Limpieza:**
* Para verificar existencia física o eliminar:
```php
$fullPath = $this->getPublicHtmlPath($record->file_path);
if ($record->file_path && file_exists($fullPath)) {
    @unlink($fullPath);
}

```


* Para crear directorios y mover archivos entrantes:
```php
$dest = $this->getPublicHtmlPath('uploads/modulo_nombre');
if (!file_exists($dest)) {
    @mkdir($dest, 0755, true);
}
$file->move($dest, $fileName);

```