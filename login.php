<?php
declare(strict_types=1);
session_start();

$error = '';
$exito = '';

// Contraseña maestra única para crear cuentas de Administrador
define('ADMIN_SECRET_KEY', 'MegaAdmin2026$');

$archivoUsuarios = __DIR__ . '/data/users.json';
if (!file_exists($archivoUsuarios)) {
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0777, true);
    }
    file_put_contents($archivoUsuarios, json_encode([], JSON_PRETTY_PRINT));
}

$usuarios = json_decode(file_get_contents($archivoUsuarios), true) ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'login';
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($accion === 'registro') {
        $nombre = trim($_POST['nombre'] ?? '');
        $rol = $_POST['rol'] ?? 'cliente';
        $admin_code = $_POST['admin_code'] ?? '';

        // Validaciones condicionales según el rol seleccionado
        if ($rol === 'admin') {
            if (empty($nombre) || empty($email) || empty($admin_code)) {
                $error = 'Por favor, completa todos los campos obligatorios.';
            } elseif ($admin_code !== ADMIN_SECRET_KEY) {
                $error = 'La contraseña maestra de administrador es incorrecta.';
            } else {
                $passwordParaGuardar = $admin_code;
            }
        } else {
            if (empty($nombre) || empty($email) || empty($password)) {
                $error = 'Por favor, completa todos los campos obligatorios.';
            } else {
                $passwordParaGuardar = $password;
            }
        }

        if (empty($error)) {
            $existe = false;
            foreach ($usuarios as $u) {
                if ($u['email'] === $email) {
                    $existe = true;
                    break;
                }
            }

            if ($existe) {
                $error = 'Este correo ya está registrado.';
            } else {
                $usuarios[] = [
                    'nombre' => $nombre,
                    'email' => $email,
                    'password' => password_hash($passwordParaGuardar, PASSWORD_DEFAULT),
                    'rol' => $rol,
                    'fecha_registro' => date('Y-m-d H:i:s')
                ];
                file_put_contents($archivoUsuarios, json_encode($usuarios, JSON_PRETTY_PRINT));
                
                // Guardar datos en sesión
                $_SESSION['user'] = $email;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['rol'] = $rol;

                header('Location: dashboard.php');
                exit;
            }
        }
    } elseif ($accion === 'login') {
        $valido = false;
        $usuarioActivo = null;

        foreach ($usuarios as $u) {
            if ($u['email'] === $email && password_verify($password, $u['password'])) {
                $valido = true;
                $usuarioActivo = $u;
                break;
            }
        }

        if ($valido && $usuarioActivo) {
            $_SESSION['user'] = $usuarioActivo['email'];
            $_SESSION['nombre'] = $usuarioActivo['nombre'] ?? 'Usuario';
            $_SESSION['rol'] = $usuarioActivo['rol'] ?? 'cliente';

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Correo o contraseña incorrectos.';
        }
    }
}

$title = 'MegaFest · Acceso a Sala de Control';
require __DIR__ . '/includes/header.php';
?>

<!-- Estilos y formulario interactivo con campos dinámicos -->
<style>
.auth-container { max-width: 440px; margin: 40px auto; background: rgba(26, 15, 35, 0.85); border: 1px solid rgba(255, 79, 216, 0.2); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); backdrop-filter: blur(10px); }
.auth-tabs { display: flex; gap: 10px; margin-bottom: 24px; background: rgba(0,0,0,0.3); padding: 4px; border-radius: 10px; }
.auth-tab { flex: 1; background: transparent; border: none; color: #bfa5cc; padding: 10px; font-weight: 600; cursor: pointer; border-radius: 8px; transition: all 0.3s ease; }
.auth-tab.active { background: #ff4fd8; color: #12061a; box-shadow: 0 0 15px rgba(255, 79, 216, 0.4); }
.auth-form { display: none; }
.auth-form.active { display: block; }
.auth-form label { display: block; margin-bottom: 16px; font-size: 0.9rem; color: #e2cceb; }
.auth-form input, .auth-form select { width: 100%; padding: 12px 14px; margin-top: 6px; background: rgba(18, 6, 26, 0.9); border: 1px solid rgba(255, 79, 216, 0.3); border-radius: 8px; color: #fff; font-size: 1rem; }
.auth-btn { width: 100%; margin-top: 10px; padding: 12px; background: linear-gradient(135deg, #ff4fd8, #9b30ff); color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
.alert-msg { background: rgba(255, 75, 75, 0.15); border: 1px solid rgba(255, 75, 75, 0.4); color: #ff9999; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; text-align: center; }
</style>

<div class="auth-container">
    <div style="text-align: center; margin-bottom: 20px;">
        <span class="kicker">Seguridad MegaFest</span>
        <h1 style="font-size: 1.8rem; margin-top: 5px;">Portal de Acceso</h1>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert-msg"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="auth-tabs">
        <button type="button" class="auth-tab active" onclick="cambiarModo('login')">Iniciar Sesión</button>
        <button type="button" class="auth-tab" onclick="cambiarModo('registro')">Registrarse</button>
    </div>

    <!-- Login -->
    <form id="form-login" class="auth-form active" method="POST">
        <input type="hidden" name="accion" value="login">
        <label>Correo electrónico <input type="email" name="email" required placeholder="tucorreo@megafest.com"></label>
        <label>Contraseña <input type="password" name="password" required placeholder="••••••••"></label>
        <button type="submit" class="auth-btn">Entrar al Panel</button>
    </form>

    <!-- Registro -->
    <form id="form-registro" class="auth-form" method="POST">
        <input type="hidden" name="accion" value="registro">
        <label>Nombre de usuario <input type="text" name="nombre" required placeholder="Tu Nombre"></label>
        <label>Correo electrónico <input type="email" name="email" required placeholder="nuevo@megafest.com"></label>
        
        <label>Tipo de cuenta 
            <select name="rol" id="rol-select" onchange="toggleAdminCode()">
                <option value="cliente">Cliente</option>
                <option value="admin">Administrador</option>
            </select>
        </label>

        <!-- Campo de contraseña para Clientes (cualquier contraseña) -->
        <label id="campo-password-cliente">Crea una contraseña 
            <input type="password" name="password" id="input-password" required placeholder="••••••••">
        </label>
        
        <!-- Campo de contraseña para Administradores (clave maestra única) -->
        <label id="admin-code-wrap" style="display: none;">Contraseña Maestra de Admin
            <input type="password" name="admin_code" id="input-admin-code" placeholder="Clave secreta de administrador">
        </label>

        <button type="submit" class="auth-btn">Crear Cuenta y Entrar</button>
    </form>
</div>

<script>
function cambiarModo(tipo) {
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    if (tipo === 'login') {
        document.querySelectorAll('.auth-tab')[0].classList.add('active');
        document.getElementById('form-login').classList.add('active');
    } else {
        document.querySelectorAll('.auth-tab')[1].classList.add('active');
        document.getElementById('form-registro').classList.add('active');
    }
}

function toggleAdminCode() {
    const rol = document.getElementById('rol-select').value;
    const wrapAdmin = document.getElementById('admin-code-wrap');
    const wrapCliente = document.getElementById('campo-password-cliente');
    const inputPassword = document.getElementById('input-password');
    const inputAdminCode = document.getElementById('input-admin-code');

    if (rol === 'admin') {
        wrapAdmin.style.display = 'block';
        wrapCliente.style.display = 'none';
        inputAdminCode.required = true;
        inputPassword.required = false;
    } else {
        wrapAdmin.style.display = 'none';
        wrapCliente.style.display = 'block';
        inputAdminCode.required = false;
        inputPassword.required = true;
    }
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
