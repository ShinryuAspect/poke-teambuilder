# Entregable 1 - Análisis y Planificación

## 1. Descripción del proyecto

**Pokémon Team Builder & Live AI Translator** es una aplicación web desarrollada en **PHP 8**, **MySQL/MariaDB**, **HTML5**, **CSS3** y **JavaScript** que permite crear y gestionar equipos competitivos de Pokémon con una interfaz dinámica inspirada en herramientas de teambuilding competitivo. El sistema consulta servicios externos para enriquecer la experiencia de usuario, especialmente la **PokéAPI** para datos de especies, sprites y movimientos, la **Smogon API** para búsquedas competitivas y la **API de Google Gemini** para análisis/traducción táctica.

El proyecto combina autenticación, persistencia en base de datos, gestión de equipos, validación de formularios y consumo de APIs externas en una solución web completa orientada a la práctica de los contenidos del curso.

## 2. Objetivos

### Objetivo general

Desarrollar una aplicación web funcional para construir, editar, analizar y almacenar equipos competitivos de Pokémon con persistencia en base de datos y consumo de APIs externas.

### Objetivos específicos

- Implementar un sistema de autenticación con registro, inicio y cierre de sesión.
- Proteger el acceso a las secciones privadas mediante control de sesión y rol.
- Construir un teambuilder dinámico con hasta 6 Pokémon por equipo.
- Consultar APIs externas para completar información de especies, sprites, movimientos y análisis competitivos.
- Almacenar equipos y su configuración en MySQL/MariaDB mediante consultas preparadas.
- Validar formularios en cliente y servidor para reducir entradas inválidas.
- Mantener una interfaz clara, usable y responsive.

## 3. Requisitos funcionales

- El usuario debe poder registrarse con nombre, correo y contraseña.
- El usuario debe poder iniciar y cerrar sesión.
- El usuario autenticado debe poder crear un equipo competitivo.
- El usuario debe poder editar y eliminar sus equipos.
- El sistema debe permitir añadir hasta 6 Pokémon por equipo.
- Cada Pokémon debe poder tener nombre, objeto, movimientos, naturaleza, EVs, IVs, género y estado shiny.
- El sistema debe consultar datos externos para completar sprites y listas de movimientos.
- El usuario debe poder analizar un equipo desde un servicio de IA externo o su integración prevista.
- La interfaz debe mostrar sugerencias dinámicas para Pokémon, objetos y movimientos.

## 4. Requisitos no funcionales

- El sistema debe funcionar sobre **PHP 8.x** y **MySQL/MariaDB**.
- La persistencia debe realizarse con **PDO** y consultas preparadas.
- Las sesiones deben gestionarse de forma segura.
- Las salidas deben sanitizarse para evitar XSS.
- La interfaz debe ser responsive y fácil de usar.
- El proyecto debe ser modular, separando vistas, modelos y servicios.
- Las peticiones a APIs externas deben resolverse de forma asíncrona siempre que sea posible.

## 5. Tecnologías utilizadas

- **Backend:** PHP 8.x
- **Base de datos:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **Persistencia:** PDO
- **APIs externas:** PokéAPI, Smogon API, Google Gemini API
- **Patrón de trabajo:** separación de responsabilidades entre vistas, modelos y servicios

## 6. Casos de uso

### Caso de uso 1: Registro de usuario

- **Actor:** visitante
- **Precondición:** no haber iniciado sesión
- **Flujo principal:** completar formulario, validar datos y crear la cuenta
- **Resultado:** usuario registrado correctamente

### Caso de uso 2: Inicio de sesión

- **Actor:** usuario registrado
- **Precondición:** existir una cuenta válida
- **Flujo principal:** introducir usuario/correo y contraseña, validar credenciales y crear sesión
- **Resultado:** acceso al dashboard

### Caso de uso 3: Crear equipo

- **Actor:** usuario autenticado
- **Precondición:** sesión activa
- **Flujo principal:** introducir nombre del equipo, elegir formato y completar los 6 slots de Pokémon
- **Resultado:** equipo guardado en la base de datos

### Caso de uso 4: Editar equipo

- **Actor:** usuario autenticado
- **Precondición:** equipo existente y perteneciente al usuario
- **Flujo principal:** abrir edición, modificar datos y guardar cambios
- **Resultado:** equipo actualizado

### Caso de uso 5: Consultar sugerencias y datos dinámicos

- **Actor:** usuario autenticado
- **Precondición:** estar en el teambuilder
- **Flujo principal:** escribir Pokémon, objetos o movimientos y recibir sugerencias
- **Resultado:** selección más rápida y guiada

### Caso de uso 6: Analizar equipo

- **Actor:** usuario autenticado
- **Precondición:** tener un equipo creado
- **Flujo principal:** solicitar análisis táctico al servicio de IA
- **Resultado:** mostrar un análisis competitivo orientativo

## 7. Cronograma de trabajo

| Fase   | Actividad                                             | Estado                                |
| ------ | ----------------------------------------------------- | ------------------------------------- |
| Fase 1 | Análisis de requisitos y definición del alcance       | Completada                            |
| Fase 2 | Diseño del modelo entidad-relación y la base de datos | Completada                            |
| Fase 3 | Desarrollo de autenticación y persistencia base       | Completada                            |
| Fase 4 | Desarrollo del teambuilder dinámico                   | Completada                            |
| Fase 5 | Integración de APIs externas                          | En progreso / parcialmente completada |
| Fase 6 | Pruebas, documentación y memoria final                | En progreso                           |

## 8. Verificación de requisitos del enunciado

### Tecnologías obligatorias

- PHP 8.x: **Cumplido**
- MySQL/MariaDB: **Cumplido**
- HTML5: **Cumplido**
- CSS3: **Cumplido**
- JavaScript: **Cumplido**

### Uso de APIs externas

- Integración de APIs externas: **Cumplido**
- PokéAPI para Pokémon, sprites y especies: **Cumplido**
- Smogon API / servicio de búsqueda competitiva: **Cumplido**
- Google Gemini API para análisis/traducción: **Parcialmente integrado y documentado**, con el servicio preparado en la arquitectura del proyecto

### Seguridad

- Validación de formularios: **Cumplido**
- Protección contra SQL Injection mediante consultas preparadas: **Cumplido**
- Gestión segura de sesiones: **Cumplido**
- Control de permisos según roles: **Cumplido**

### Observaciones

- La clave de Gemini aparece configurada localmente en el archivo de configuración del entorno de desarrollo. Para una entrega final, se recomienda moverla a variables de entorno.
- El proyecto está funcional para autenticación, persistencia y construcción de equipos; la capa de IA está preparada y documentada como extensión de la solución.

## 9. Conclusión del análisis

El proyecto cumple con la base técnica pedida por el enunciado: usa PHP 8, MySQL/MariaDB, HTML5, CSS3 y JavaScript, incorpora autenticación, persistencia y consumo de APIs externas, y aplica validación, sesiones y control de acceso. La arquitectura del código también separa responsabilidades entre vistas, modelos y servicios, lo que facilita mantenimiento y evolución.
