# Patrón de Arquitectura Desacoplada y Estándares de Seguridad para Controladores

Este documento define la arquitectura obligatoria de 4 capas y los estándares de seguridad y concurrencia que deben aplicarse a todos los controladores refactorizados en este proyecto.

---

## 🏛️ Estructura de 4 Capas

1. **Controlador (`App\Http\Controllers\...`)**:
   - Inyecta únicamente `UtilResponse` y el Repositorio de la entidad vía constructor.
   - Responsabilidad única: orquestar el flujo HTTP, delegando validación, persistencia, serialización y respuestas.
   - **Resiliencia y Observabilidad:**
     - Envolver las operaciones en bloques `try / catch (\Throwable $e)`.
     - Registrar errores inesperados con `Log::error(...)` incluyendo contexto (`action`, `user_id`, `payload`, `exception`).
     - Responder con `$this->utilResponse->errorResponse('Mensaje seguro para usuario', 500)` sin fugar detalles internos ni trazas de base de datos.

2. **FormRequest (`App\Http\Requests\<Entity>\<Entity>Request`)**:
   - Centraliza la validación de entrada (`rules()`) para `POST` (creación) y `PUT`/`PATCH` (actualización ignorando el ID actual para reglas `unique`).
   - `messages()`: Mensajes legibles y profesionales en español.
   - **Sanitización Defensiva (Anti Stored-XSS):**
     - Implementar `prepareForValidation()` para aplicar `strip_tags(trim(...))` sobre todas las entradas de texto antes de ser validadas y persistidas.
   - **Autorización:**
     - Por defecto `authorize()` retorna `true` a menos que el usuario especifique explícitamente políticas o permisos específicos.

3. **Repositorio (`App\Http\Repositories\<Entity>\<Entity>Repository`)**:
   - Inyecta el modelo Eloquent en el constructor.
   - Métodos estándar: `all()`, `find($id)`, `create(array $data)`, `update($id, array $data)`, `delete($id)` y métodos de consulta de relaciones (e.g. `hasEquipment($id)`).
   - **Transaccionalidad ACID y Concurrencia:**
     - Envolver todas las mutaciones (`create`, `update`, `delete`) dentro de `DB::transaction()`.
     - En `update()` y `delete()`, usar **bloqueo pesimista (`lockForUpdate()`)** para prevenir condiciones de carrera TOCTOU (*Time-of-Check to Time-of-Use*).
     - Usar métodos óptimos de existencia en relaciones (`exists()` en lugar de `count() > 0`).

4. **Resource (`App\Http\Resources\<Entity>\<Entity>Resource`)**:
   - Extiende `JsonResource`.
   - Transforma atributos de forma tipada, exponiendo únicamente los campos necesarios y formateando fechas con `d-m-Y H:i:s`.

5. **UtilResponse (`App\Traits\UtilResponse`)**:
   - Estandariza respuestas JSON garantizando compatibilidad con Blade/jQuery (`success`) y APIs (`flag`):
     - `successResponse($data, $message, $code = 200)` -> `{"success": true, "flag": true, "code": $code, "message": $message, "data": $data}`
     - `errorResponse($message, $code = 404)` -> `{"success": false, "flag": false, "code": $code, "message": $message, "data": []}`
