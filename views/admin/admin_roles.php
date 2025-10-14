<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión correctamente
require_once __DIR__ . '/../../core/SessionManager.php';
SessionManager::start();

// Verificar si el usuario está logueado
$user = SessionManager::getUser();
if (!$user) {
    header("Location: /index.php?expired=1");
    exit;
}

// Verificar rol
if (!isset($user['role']) || $user['role'] !== 'admin') {
    die("🚫 Acceso restringido: esta página es solo para usuarios Administrador.");
}

// Opcional: datos del usuario
$usuario = $user['username'] ?? 'Sin usuario';
$email = $user['email'] ?? 'Sin email';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Impulsa SO - Admin</title>

    <!-- Íconos de Material Design -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <!-- Framework Success desde CDN -->
    <link rel="stylesheet" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css">
    <script src="https://www.fernandosalguero.com/cdn/assets/javascript/framework.js" defer></script>
</head>

<body>
    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <span class="material-icons logo-icon">dashboard</span>
                <span class="logo-text">ISO</span>
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li onclick="location.href='admin_dashboard.php'">
                        <span class="material-icons" style="color: #5b21b6;">home</span><span class="link-text">Inicio</span>
                    </li>
                    <li onclick="location.href='admin_roles.php'">
                        <span class="material-icons" style="color: #5b21b6;">person</span><span class="link-text">Usuarios</span>
                    </li>
                    <li onclick="location.href='../../../logout.php'">
                        <span class="material-icons" style="color: red;">logout</span><span class="link-text">Salir</span>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <button class="btn-icon" onclick="toggleSidebar()">
                    <span class="material-icons" id="collapseIcon">chevron_left</span>
                </button>
            </div>
        </aside>

        <div class="main">
            <header class="navbar">
                <button class="btn-icon" onclick="toggleSidebar()">
                    <span class="material-icons">menu</span>
                </button>
                <div class="navbar-title">Inicio</div>
            </header>

            <section class="content">
                <div class="card">
                    <h2>Hola 👋 <?= htmlspecialchars($usuario) ?></h2>
                    <iso>En esta página, vemos los usuarios registrados y su información personal junto con sus roles y permisos.</p>
                </div>

                <!-- Formulario -->
                <div class="card">
                    <h2>Formularios</h2>
                    <form class="form-modern">
                        <div class="form-grid grid-4">

                            <!-- Nombre completo -->
                            <div class="input-group">
                                <label for="nombre">Nombre</label>
                                <div class="input-icon input-icon-name">
                                    <input type="text" id="nombre" name="nombre" placeholder="Juan Pérez" />
                                </div>
                            </div>

                            <!-- Correo electrónico -->
                            <div class="input-group">
                                <label for="email">Correo electrónico</label>
                                <div class="input-icon input-icon-email">
                                    <input id="email" name="email" placeholder="usuario@correo.com" />
                                </div>
                            </div>

                            <!-- DNI -->
                            <div class="input-group">
                                <label for="dni">DNI</label>
                                <div class="input-icon input-icon-dni">
                                    <input id="dni" name="dni" />
                                </div>
                            </div>

                            <!-- User name -->
                            <div class="input-group">
                                <label for="nombre">Usuario</label>
                                <div class="input-icon input-icon-name">
                                    <input type="text" id="usuario" name="usuario" placeholder="Usuario" />
                                </div>
                            </div>
                        </div>

                    </form>
                </div>


                <!-- tabla de usuarios -->
                <div class="card tabla-card">
                    <h2>Tablas</h2>
                    <div class="tabla-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>DNI</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Carlos Guimenez</td>
                                    <td>Ruiz@ruiz.com.ar</td>
                                    <td>56987456</td>
                                    <td>Client</td>
                                    <td><span class="badge success">Activo</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Laura Peralta</td>
                                    <td>Méndez@mendez.com.ar</td>
                                    <td>98756321</td>
                                    <td>Client</td>
                                    <td><span class="badge warning">Pendiente</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>
        </div>
    </div>


    <!-- Scripts -->

    

    <script src="../../views/partials/spinner-global.js"></script>

    <script>
        console.log(<?php echo json_encode($_SESSION); ?>);
    </script>
</body>

</html>