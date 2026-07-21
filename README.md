# Sistema de Intranet para el Colegio José Granda

## 📋 Descripción del Proyecto
El **Sistema de Intranet para el Colegio José Granda** es una plataforma web desarrollada en modalidad intranet diseñada para automatizar, digitalizar y optimizar el proceso de matrícula escolar. El sistema centraliza la gestión de estudiantes, apoderados, control de vacantes por secciones, validación de pagos y generación automática de comprobantes y reportes en formato PDF.

La arquitectura del software está construida bajo un esquema modular en **N-Capas**, implementando el patrón de diseño arquitectónico **MVC (Modelo-Vista-Controlador)** combinado con **DAO (Data Access Object)**.

---

## 🛠️ Tecnologías y Requisitos Previos

Este proyecto requiere obligatoriamente de un entorno de servidor local y un motor de base de datos relacional para funcionar de manera correcta. No es posible ejecutar el sistema sin una base de datos local conectada.

* **Servidor Local:** [XAMPP](https://www.apachefriends.org/es/index.html) (Apache / PHP 7.4 o superior).
* **Base de Datos:** [PostgreSQL](https://www.postgresql.org/) (Versión 12 o superior).
* **Herramienta de Gestión de BD:** PgAdmin 4.
* **Frontend:** HTML5, CSS3, JavaScript.
* **Librerías Externas:** FPDF (para generación de comprobantes y reportes PDF).

---

## ⚙️ Estructura del Proyecto

El código fuente se encuentra organizado bajo una estricta separación de responsabilidades:

```text
/
├── app/
│   ├── config/       # Archivos de conexión a la base de datos (conexion.php)
│   ├── controller/   # Lógica de negocio y controladores (PHP)[cite: 3]
│   ├── dao/          # Objetos de Acceso a Datos (Operaciones CRUD y consultas SQL)[cite: 3]
│   ├── models/       # Clases de entidades y DTOs[cite: 3]
│   └── views/        # Interfaces de usuario organizadas por módulos (admin, matricula, login, etc.)[cite: 3]
├── public/           # Recursos estáticos centralizados (CSS, imágenes, JS, videos)[cite: 3]
├── libraries/        # Librerías de terceros (FPDF)[cite: 3]
└── tests/            # Archivos de prueba del sistema
