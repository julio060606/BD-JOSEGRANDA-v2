<?php
// header.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include("conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Colegio — Panel Admin</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Estilos -->
  <link rel="stylesheet" href="CSS/admin.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container-fluid ps-4">
    <!-- Botón hamburguesa para móvil -->
    <button class="btn btn-dark d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
      <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Logo -->
    <a class="navbar-brand fw-bold navbar-logo" href="#">Colegio — Panel Admin</a>

    <!-- Opciones a la derecha -->
    <div class="d-flex align-items-center ms-auto flex-wrap gap-2">
      <button class="btn btn-outline-primary me-3 d-none d-md-inline-flex">
        <i class="fa-solid fa-user-plus me-2"></i>Registrar Alumno
      </button>
      <button class="btn btn-outline-success me-3 d-none d-md-inline-flex">
        <i class="fa-solid fa-file-contract me-2"></i>Matricular
      </button>
      <div class="dropdown">
        <a class="d-flex align-items-center text-decoration-none" href="#" data-bs-toggle="dropdown">
          <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="avatar">
          <strong>Admin</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Perfil</a></li>
          <li><a class="dropdown-item" href="#">Ajustes</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="#">Cerrar sesión</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="d-flex">
  <!-- Sidebar -->
  <aside class="offcanvas-lg offcanvas-start sidebar bg-dark text-white" tabindex="-1" id="sidebar">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Menu</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
      <ul class="nav nav-pills flex-column">
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="admin.php"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard</a></li>
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="padres.php"><i class="fa-solid fa-user-tie me-2"></i>Padres</a></li>
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="alumnos.php"><i class="fa-solid fa-user-graduate me-2"></i>Alumnos</a></li>
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="pagos.php"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Pagos</a></li>
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="servicios.php"><i class="fa-solid fa-chalkboard-user me-2"></i>Servicios</a></li>
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="reporte_alumnos.php"><i class="fa-solid fa-file-export me-2"></i>Reportes</a></li>
      </ul>
    </div>
  </aside>

  <!-- Main content -->
  <main class="main-content p-4 flex-grow-1 w-100">
