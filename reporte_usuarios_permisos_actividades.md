# Reporte de Auditoría: Usuarios, Roles, Permisos y Actividades Operativas

Este documento presenta el análisis exhaustivo del sistema ERP (Laravel) y la base de datos de producción (`success`), detallando los **roles asignados**, **módulos accesibles**, **permisos Spatie RBAC** y las **actividades operativas reales registradas en base de datos** para el personal clave solicitado.

---

## 📊 Matriz Resumen de Responsabilidades

| Colaborador | Rol Principal | Área / Módulo Dominante en la BD | Métricas Clave en BD |
| :--- | :--- | :--- | :--- |
| **Antonio González** | `Warehouse` | Almacén y Control Ambiental | 192 lecturas de temperatura, Entradas/Salidas, 18 Comparativos |
| **Flor de María Gutiérrez Sánchez** | `Sales` | Ventas Comerciales y Muestras | 103 Ventas creadas, 48 Solicitudes de Muestras, 19 Cotizaciones |
| **Manola Ramírez** | `Admin` | Dirección Comercial y Finanzas (CXP) | 213 Ventas, 13 Pagos CXP ($36.6k MXN), 16 Solicitudes de Lote |
| **William Mc Lane** | `Admin` | Ventas y Operación Directiva | 254 Ventas asignadas, 24 Facturas/Bancos, 57 Entradas |
| **Rocío Galván** | `Admin` | Logística, Tráfico y Almacén | 141 Entradas almacén, 57 Salidas, Embarques/Órdenes |
| **Cristhel Campa** | `Quality` | Inspecciones y Calidad de Planta | 94 Inspecciones formales, 272 Certificados PDF, 14 Incidencias |
| **Etthan Juárez** | `Laboratory` | Recepción de Muestras y Reactivos | 116 Muestras recibidas, 76 Muestreos, 61 Comparativos |
| **Stefany Hernández Saucillo** | `Admin` | Abastecimiento y Compras Generales | 30 Comparativos de compras, 10 Requisiciones |
| **Claudio Rugarcia Anaya** | `Production` | Operación de Planta y Producción | 47 Entradas, 44 Salidas, 26 Muestreos, 17 Comparativos |
| **Emilio Vázquez** | `Admin` | Soporte Técnico y Administración ERP | Pruebas, Auditoría, Gestión TI y Portal Clientes |
| **Noemy** | `Laboratory` | Muestreo y Recepción en Laboratorio | 32 Muestras entregadas, 4 Muestreos, 2 Comparativos |

---

## 👤 Desglose Individual por Colaborador

### 1. Antonio González
* **ID Usuario:** `6`
* **Correo Electrónico:** `pgonzalez@suministrossustentables.com`
* **Rol del Sistema:** `Warehouse` (Almacén)
* **Permisos del Rol (26 permisos):**
  * `warehouse.show`, `warehouse.create`, `warehouse.delete`, `warehouse.temperature`
  * `inventory.show`, `inventory.create-transaction`, `inventory.create-tweak`, `inventory.alter-date`
  * `quarantine.show`, `quarantine.modify`
  * `products.show`, `products.create`, `products.update`, `products.delete`
  * `suppliers.show`, `suppliers.create`, `suppliers.update`, `suppliers.delete`
  * `customers.show`
  * `purchases.show`, `purchases.warehouse`, `purchases.requisitions.create`, `purchases.requisitions.update`, `purchases.requisitions.delete`
  * `quality.show`, `quality.updateW`
* **Actividades Reales en Base de Datos:**
  * **Monitoreo Ambiental:** **192 registros** de temperatura y humedad en la tabla `control`.
  * **Operación de Almacén:** Operador en movimientos de entradas (`inputs`) y salidas (`outputs`).
  * **Ajustes de Inventario:** 4 ajustes formales registrados en `tweaks`.
  * **Compras y Requisiciones:** 18 registros en cuadros comparativos (`comparative`) y 7 requisiciones de compras (`purchases_requisitions`).
  * **Muestreo:** Registro y recepción de muestras físicas en almacén (`reception_of_samples`).

---

### 2. Flor de María Gutiérrez Sánchez
* **ID Usuario:** `7`
* **Correo Electrónico:** `fgutierrez@suministrossustentables.com`
* **Rol del Sistema:** `Sales` (Ventas)
* **Permisos del Rol (9 permisos):**
  * `sales.show`, `sales.create`
  * `customers.show`
  * `products.show`, `inventory.show`
  * `purchases.show`, `purchases.requisitions.create`, `purchases.requisitions.update`, `purchases.requisitions.delete`
* **Actividades Reales en Base de Datos:**
  * **Ventas Comerciales:** **103 ventas creadas** en el sistema y **114 ventas asignadas** como vendedora titular (`seller`).
  * **Cotizaciones:** **19 cotizaciones formales** generadas en `quotes`.
  * **Muestras a Clientes:** **48 solicitudes de muestras** generadas y **35 seguimientos** activos en `customer_sample_requests`.
  * **Despachos:** 23 salidas comerciales de producto coordinadas con almacén (`outputs`).

---

### 3. Manola Ramírez
* **ID Usuario:** `9`
* **Correo Electrónico:** `mramirez@suministrossustentables.com`
* **Rol del Sistema:** `Admin` (Administrador General — 58 permisos globales)
* **Permisos del Rol:** Acceso total a todos los módulos (Ventas, Finanzas, Compras, Calidad, Almacén, Laboratorio, RH, Producción, Minutas, etc.).
* **Actividades Reales en Base de Datos:**
  * **Ventas y Cartera:** **213 ventas creadas** directamente en `sales` y 32 clientes en cartera activa.
  * **Finanzas / Cuentas por Pagar (CXP):** **13 pagos a proveedores registrados** en `cxp_payments` por un monto total de **$36,695.30 MXN**. Registrada como pagadora en facturas de compras.
  * **Laboratorio y Muestreo:** 18 recepciones de muestras firmadas (`reception_of_samples`), 12 recolecciones (`laboratory_samples`) y 16 solicitudes de lote (`lot_requests`).
  * **Cotizaciones y Gestión:** 3 cotizaciones creadas en `quotes` y acuerdos directivos en minutas.

---

### 4. William Mc Lane
* **ID Usuario:** `14`
* **Correo Electrónico:** `wmclane@suministrossustentables.com`
* **Rol del Sistema:** `Admin` (Administrador General — 58 permisos globales)
* **Permisos del Rol:** Acceso total a todos los módulos del ERP.
* **Actividades Reales en Base de Datos:**
  * **Ventas:** **101 ventas registradas** en sistema y **254 ventas asignadas** como vendedor titular (`sales`).
  * **Cotizaciones:** **12 cotizaciones formales** generadas en `quotes`.
  * **Tesorería / Finanzas:** Registrado como pagador oficial en **24 facturas de compras y cuentas bancarias**.
  * **Operación de Almacén:** Operador en **57 entradas (`inputs`)** y **29 salidas (`outputs`)**.
  * **Laboratorio y Muestreo:** 12 firmas de recepción de muestras en laboratorio, 11 solicitudes técnicas y 8 recolecciones.
  * **Dirección Operativa:** Ponente y asistente en reuniones y acuerdos (`minutas`).

---

### 5. Rocío Galván
* **ID Usuario:** `22`
* **Correo Electrónico:** `mgalvan@suministrossustentables.com`
* **Rol del Sistema:** `Admin` (Administrador General — 58 permisos globales)
* **Permisos del Rol:** Acceso total a nivel de plataforma.
* **Actividades Reales en Base de Datos:**
  * **Logística y Almacén (Operación Pesada):** Registrada como operadora en **141 entradas (`inputs`)** y **57 salidas (`outputs`)** de producto e insumos.
  * **Ventas y Despachos:** 73 salidas de producto coordinadas con clientes y vendedores (`outputs`).
  * **Transporte:** Coordinación y seguimiento de unidades de transporte en el módulo de órdenes (`orders`).
  * **Compras y Finanzas:** Registro de cuadros comparativos (`comparative`), pagadora en facturas y consultas frecuentes de reportes oficiales.

---

### 6. Cristhel Campa
* **ID Usuario:** `24`
* **Correo Electrónico:** `ccampa@suministrossustentables.com`
* **Rol del Sistema:** `Quality` (Control de Calidad)
* **Permisos del Rol (17 permisos):**
  * `quality.show`, `quality.buttons.show`, `quality.update`, `quality.delete`, `quality.purchases`, `fumigation.show`
  * `laboratory.show`, `laboratory.quality`
  * `inventory.show`, `purchases.show`, `purchases.requisitions.create/update/delete`
  * `products.show`, `products.update`, `suppliers.show`, `customers.show`
* **Actividades Reales en Base de Datos:**
  * **Inspecciones de Calidad / Planta:** **94 inspecciones formales completadas** en la tabla `inspections_w` (Inspectora titular).
  * **Certificados de Calidad:** **272 consultas/descargas de certificados oficiales en PDF** (`pdf_clicks`).
  * **Incidencias:** **14 firmas de incidencias** de calidad y no conformidades registradas en `incidencias`.
  * **Muestreo:** 11 firmas de entrega en recepción de muestras y 9 recolecciones en laboratorio.
  * **Requisiciones de Calidad:** 16 comparativas de compras (`comparative`) y 7 requisiciones para insumos de control de calidad.

---

### 7. Etthan Juárez
* **ID Usuario:** `28`
* **Correo Electrónico:** `ajuarez@suministrossustentables.com`
* **Rol del Sistema:** `Laboratory` (Laboratorio)
* **Permisos del Rol (11 permisos):**
  * `laboratory.show`, `laboratory.update`, `laboratory.delete`
  * `purchases.show`, `purchases.requisitions.create`, `purchases.requisitions.show`, `purchases.requisitions.update`, `purchases.requisitions.delete`
  * `products.show`, `warehouse.show`, `inventory.show`
* **Actividades Reales en Base de Datos:**
  * **Recepción Técnica de Muestras:** **116 recepciones de muestras firmadas** y **48 entregas** en `reception_of_samples` (Responsable principal de ingreso al laboratorio).
  * **Recolección y Muestreo:** **76 muestras recolectadas y procesadas** en `laboratory_samples`.
  * **Compras de Laboratorio:** **61 cuadros comparativos de compras (`comparative`)** y **23 requisiciones de compra (`purchases_requisitions`)** para reactivos, material de vidrio e insumos de análisis.

---

### 8. Stefany Hernández Saucillo
* **ID Usuario:** `30`
* **Correo Electrónico:** `shernandez@suministrossustentables.com`
* **Rol del Sistema:** `Admin` (Administrador General — 58 permisos globales)
* **Permisos del Rol:** Acceso global administrativo.
* **Actividades Reales en Base de Datos:**
  * **Abastecimiento y Compras:** **30 cuadros comparativos de compras** (`comparative`) y **10 requisiciones de compra** generadas para múltiples departamentos.
  * **Cotizaciones:** 1 cotización comercial registrada en `quotes`.
  * **Infraestructura y TI:** Responsable de 2 equipos en `it_equipments` e inspecciones de TI (`it_inspections`).
  * **Auditoría:** Registro de modificaciones/eliminaciones en el log de auditoría (`activity_log`) y descarga de reportes PDF oficiales (`pdf_clicks`).

---

### 9. Claudio Rugarcia Anaya
* **ID Usuario:** `32`
* **Correo Electrónico:** `crugarcia@suministrossustentables.com`
* **Rol del Sistema:** `Production` (Producción)
* **Permisos del Rol (14 permisos):**
  * `production`, `production.show`, `id.show`
  * `minutas.show`
  * `warehouse.show`, `inventory.show`, `products.show`, `sales.create`, `sales.show`, `customers.show`
  * `purchases.show`, `purchases.requisitions.create/update/delete`
* **Actividades Reales en Base de Datos:**
  * **Operaciones de Planta:** Operador en **47 entradas de materias primas (`inputs`)** y **44 salidas de producto procesado (`outputs`)**.
  * **Muestreo de Producción:** **26 recolecciones de muestras de producción** en `laboratory_samples` y 9 entregas a laboratorio.
  * **Mantenimiento y Compras de Planta:** **17 comparativos de compras (`comparative`)** y **15 requisiciones** para mantenimiento, herramientas y suministros de producción.
  * **Minutas:** Seguimiento a acuerdos operativos de planta.

---

### 10. Emilio Vázquez
* **ID Usuario:** `34`
* **Correo Electrónico:** `emiliovpsis@gmail.com`
* **Rol del Sistema:** `Admin` (Administrador / Soporte de Desarrollo)
* **Permisos del Rol:** Acceso total a todos los módulos.
* **Actividades Reales en Base de Datos:**
  * **Soporte y Pruebas del Sistema:** **26 eventos de auditoría en `activity_log`** (creación, edición y eliminación de datos de prueba y validación).
  * **Portal de Clientes:** Gestión y pruebas de reseteo de contraseñas de usuarios del portal (`portal_password_resets`).
  * **TI y Compras:** 3 cuadros comparativos en `comparative`, 1 requisición y asignación de equipo en `it_equipments`.

---

### 11. Noemy
* **ID Usuario:** `35`
* **Correo Electrónico:** `nprado@suministrossustentables.com`
* **Rol del Sistema:** `Laboratory` (Laboratorio)
* **Permisos del Rol (11 permisos):**
  * `laboratory.show`, `laboratory.update`, `laboratory.delete`
  * `purchases.show`, `purchases.requisitions.create/show/update/delete`
  * `inventory.show`, `warehouse.show`, `products.show`
* **Actividades Reales en Base de Datos:**
  * **Recepción y Entrega de Muestras:** **32 entregas de muestras físicas documentadas y firmadas** en `reception_of_samples`.
  * **Muestreo:** 4 recolecciones de muestras registradas en `laboratory_samples`.
  * **Compras de Laboratorio:** 2 cuadros comparativos en `comparative` y 1 requisición formal de insumos para el laboratorio.
