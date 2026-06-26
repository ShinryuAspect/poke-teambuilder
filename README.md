# 🚀 Pokémon Team Builder & Live AI Translator

Un creador de equipos competitivos de Pokémon desarrollado en **PHP 8 (POO)** y **MySQL**, integrado con la API de Smogon para la extracción de métricas y sets de movimientos, combinando el potencial de la Inteligencia Artificial mediante la **API de Google Gemini** para ofrecer traducción y adaptación técnica en vivo al castellano.

Este proyecto ha sido desarrollado como trabajo final para el curso de especialización profesional en Desarrollo Web.

---

## 🛠️ Arquitectura y Tecnologías Utilizadas

* **Backend:** PHP 8.x (Programación Orientada a Objetos, PDO, Gestión de Sesiones Segura).
* **Base de Datos:** MySQL / MariaDB (Motor InnoDB, Restricciones de integridad referencial y borrado en cascada).
* **Frontend:** HTML5 semántico, CSS3 moderno (Flexbox & Grid Layout) y JavaScript nativo (Vanilla JS) mediante consumo asíncrono con **Fetch API**.
* **APIs Externas Integradas:**
    * Smogon API - Repositorio de datos competitivos, formatos (Tiers) y estrategias.
    * Google Gemini API (1.5 Flash) - Traducción contextual, interpretación y adaptación de la terminología técnica del metajuego.

---

## 🔑 Características Clave e Implementación de Requisitos

* **Seguridad Primero:**
    * Prevención total de *SQL Injection* mediante el uso estricto de **consultas preparadas con PDO**.
    * Almacenamiento seguro de credenciales utilizando hashes criptográficos robustos con `password_hash()`.
    * Control de acceso perimetral según el rol del usuario (`user` / `admin`).
    * Sanitización de datos de salida con `htmlspecialchars()` para prevenir ataques XSS (Cross-Site Scripting).
* **Optimización del rendimiento (Capa de Caché):** Enfoque híbrido mediante una tabla local `traducciones_cache` que almacena los textos traducidos previamente por la IA de Gemini indexados por hash SHA-1. Esto reduce la latencia de carga de segundos a milisegundos y mitiga el agotamiento de cuota de la API gratuita.
* **Team Builder Dinámico:** Creación, edición, lectura y borrado (CRUD) de combinaciones de hasta 6 Pokémon por equipo, exportables al formato universal estructurado de *Pokémon Showdown*.

---

## 📂 Estructura del Proyecto

El proyecto sigue una arquitectura limpia que separa las responsabilidades del sistema (Lógica / Vistas / Endpoints asíncronos):

```text
poke-teambuilder/
├── config/              # Parámetros globales y conexión PDO (Singleton)
├── src/                 # Núcleo del Backend (POO)
│   ├── Models/          # Entidades de Base de Datos (Usuario, Equipo, Pokemon)
│   └── Services/        # Capa de servicios externos (GeminiService, SmogonAPI)
├── public/              # Directorio raíz del servidor web público
│   ├── api/             # Endpoints asíncronos que devuelven JSON
│   ├── assets/          # Recursos estáticos (CSS, JS, imágenes locales)
│   └── views/           # Plantillas y componentes visuales de la interfaz
└── docs/                # Documentación del proyecto y esquemas de BD
```

---

## 🚀 Instalación y Configuración Local

Sigue estos pasos para replicar el entorno de desarrollo localmente:

### 1. Clonar el repositorio
Para descargar el código en tu máquina local, ejecuta:
```bash
git clone [https://github.com/tu-usuario/nombre-del-repositorio.git](https://github.com/tu-usuario/nombre-del-repositorio.git)
cd nombre-del-repositorio
```

### 2. Configurar la Base de Datos
1. Inicia tu servidor local de bases de datos (XAMPP, Laragon, etc.).
2. Importa el archivo ubicado en `docs/bd_schema.sql` en tu gestor de bases de datos (PHPMyAdmin/MariaDB).
3. Asegúrate de configurar tus credenciales de conexión en el archivo de configuración global `config/config.php`.

### 3. Configurar Variables de Entorno / API Key
Para que la traducción en vivo funcione, necesitas una clave de API de Google AI Studio. Define tu constante en tu archivo de configuración de esta manera:

```php
define('GEMINI_API_KEY', 'TU_API_KEY_AQUI');
```

---

## 🎯 Plan de Trabajo y Entregables (Hoja de Ruta)

* [x] **Entregable 1:** Análisis, Planificación y Definición de Requisitos.
* [x] **Entregable 2:** Diseño Conceptual, Modelo Entidad-Relación y Script DDL.
* [ ] **Entregable 3:** Desarrollo del Sistema de Autenticación, persistencia base y pruebas de interconexión con APIs.
* [ ] **Entregable 4:** Maquetación del Frontend, implementación de caché de traducción, memoria final y presentación.

---

## 📝 Licencia

Este proyecto es de uso estrictamente académico e informativo. Los datos relativos a las marcas, mecánicas y criaturas de Pokémon son propiedad de *The Pokémon Company*, *Game Freak* y *Nintendo*.
