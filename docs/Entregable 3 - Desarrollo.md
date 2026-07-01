# Entregable 3 - Desarrollo

## 1. Base de datos creada

La base de datos del proyecto se encuentra definida en `docs/bd_schema.sql` y contempla las entidades principales necesarias para la aplicación:

- `usuarios`: credenciales y rol.
- `equipos`: cabecera de cada equipo competitivo.
- `pokemon`: detalle de cada integrante del equipo.
- `traducciones_cache`: caché para traducciones generadas por IA.

Además, el modelo de persistencia utiliza motor **InnoDB**, claves foráneas y borrado en cascada para mantener la integridad referencial.

## 2. Sistema de autenticación funcional

El sistema de autenticación está implementado con:

- Registro de usuarios con validación de correo y contraseña.
- Inicio de sesión mediante usuario o correo electrónico.
- Cierre de sesión mediante destrucción de la sesión.
- Redirección automática a páginas protegidas si no existe sesión activa.
- Regeneración de ID de sesión tras login para reforzar la seguridad.

### Evidencias funcionales

- `public/views/registro.php`
- `public/views/login.php`
- `public/views/logout.php`
- `public/views/header.php`
- `src/models/Usuario.php`

## 3. Primeras funcionalidades implementadas

### 3.1 Dashboard de equipos

El usuario autenticado puede ver un listado de sus equipos y acceder a acciones de edición, análisis y eliminación.

### 3.2 Constructor de equipos

El teambuilder permite:

- Crear hasta 6 Pokémon por equipo.
- Buscar Pokémon y objetos con sugerencias dinámicas.
- Cargar sprites y tipos desde datos externos.
- Definir movimientos, naturaleza, EVs, IVs, género y shiny.
- Calcular estadísticas finales de forma dinámica.

### 3.3 Edición de equipos

La pantalla de edición replica la lógica del creador y permite modificar la composición del equipo sin recrearlo desde cero.

### 3.4 Búsqueda competitiva

El proyecto incluye un servicio de búsqueda de especies basado en datos externos para reforzar la selección de Pokémon.

### 3.5 Análisis táctico

Existe la base de integración con Gemini para exponer un análisis táctico del equipo. En el estado actual del proyecto, la ruta de análisis está estructurada y lista para conectarse con una implementación completa del servicio.

## 4. Consumo de APIs externas

### PokéAPI

Se usa para:

- Listado global de Pokémon.
- Listado global de objetos.
- Carga de sprites y sprites alternativos.
- Tipos, estadísticas base y movimientos por especie.
- Información de género y diferencias entre macho/hembra cuando existen.

### Smogon / búsqueda competitiva

Se emplea un servicio de consulta de especies para reforzar la experiencia de búsqueda competitiva.

### Google Gemini

El proyecto está preparado para integrar análisis y traducción táctica mediante Gemini. La capa de servicio se contempla en la arquitectura y el endpoint de análisis ya está previsto.

## 5. Pruebas realizadas

### Pruebas de sintaxis

Se revisó la sintaxis PHP de los archivos clave con `php -l` sin errores.

### Pruebas funcionales de validación

- Validación de formulario de registro.
- Validación de formulario de inicio de sesión.
- Bloqueo de acceso si no existe sesión.
- Carga dinámica de datos del Pokémon al seleccionar un nombre válido.
- Recalculo automático de estadísticas al cargar un Pokémon o modificar EVs, IVs o naturaleza.

### Pruebas de persistencia

- Guardado de equipos en la base de datos.
- Actualización de equipos existentes.
- Lectura de equipos del usuario autenticado.

## 6. Resultado del desarrollo

El proyecto ya dispone de una base funcional sólida para cumplir el objetivo académico del curso: autenticación, persistencia, CRUD de equipos, teambuilder dinámico y consumo de servicios externos. La integración completa de IA queda planteada como extensión directa de la arquitectura actual.

## 7. Observaciones técnicas

- Se recomienda mover las claves de API a variables de entorno para el despliegue final.
- La documentación y la estructura del código ya están preparadas para una ampliación futura de la capa de IA.
- El diseño actual prioriza una experiencia de usuario rápida y sencilla, manteniendo la lógica en el frontend y la persistencia en el backend.
