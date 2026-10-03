<?php
declare(strict_types=1);
session_start();

$error = '';
$exito = '';

// Ruta al archivo de usuarios
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

    if (empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        if ($accion === 'registro') {
            // Verificar si ya existe
            $existe = false;
            foreach ($usuarios as $u) {
                if ($u['email'] === $email) {
                    $existe = true;
                    break;
                }
            }

            if ($existe) {
                $error = 'Este correo ya está registrado. Inicia sesión.';
            } else {
                // Registrar nuevo usuario
                $usuarios[] = [
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'fecha_registro' => date('Y-m-d H:i:s')
                ];
                file_put_contents($archivoUsuarios, json_encode($usuarios, JSON_PRETTY_PRINT));
                
                $_SESSION['user'] = $email;
                header('Location: dashboard.php');
                exit;
            }
        } elseif ($accion === 'login') {
            // Validar inicio de sesión
            $valido = false;
            foreach ($usuarios as $u) {
                if ($u['email'] === $email && password_verify($password, $u['password'])) {
                    $valido = true;
                    break;
                }
            }

            if ($valido) {
                $_SESSION['user'] = $email;
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Correo o contraseña incorrectos, o usuario no registrado.';
            }
        }
    }
}

$title = 'MegaFest · Acceso a Sala de Control';
require __DIR__ . '/includes/header.php';
?>

<style>
.auth-container {
    max-width: 440px;
    margin: 40px auto;
    background: rgba(26, 15, 35, 0.85);
    border: 1px solid rgba(255, 79, 216, 0.2);
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    backdrop-filter: blur(10px);
}
.auth-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 24px;
    background: rgba(0,0,0,0.3);
    padding: 4px;
    border-radius: 10px;
}
.auth-tab {
    flex: 1;
    background: transparent;
    border: none;
    color: #bfa5cc;
    padding: 10px;
    font-weight: 600;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.auth-tab.active {
    background: #ff4fd8;
    color: #12061a;
    box-shadow: 0 0 15px rgba(255, 79, 216, 0.4);
}
.auth-form {
    display: none;
}
.auth-form.active {
    display: block;
}
.auth-form label {
    display: block;
    margin-bottom: 16px;
    font-size: 0.9rem;
    color: #e2cceb;
}
.auth-form input {
    width: 100%;
    padding: 12px 14px;
    margin-top: 6px;
    background: rgba(18, 6, 26, 0.9);
    border: 1px solid rgba(255, 79, 216, 0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 1rem;
}
.auth-form input:focus {
    border-color: #ff4fd8;
    outline: none;
    box-shadow: 0 0 8px rgba(255, 79, 216, 0.3);
}
.auth-btn {
    width: 100%;
    margin-top: 10px;
    padding: 12px;
    background: linear-gradient(135deg, #ff4fd8, #9b30ff);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: opacity 0.2s;
}
.auth-btn:hover {
    opacity: 0.9;
}
.alert-msg {
    background: rgba(255, 75, 75, 0.15);
    border: 1px solid rgba(255, 75, 75, 0.4);
    color: #ff9999;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    text-align: center;
}
</style>

<div class="auth-container">
    <div style="text-align: center; margin-bottom: 20px;">
        <span class="kicker">Seguridad MegaFest</span>
        <h1 style="font-size: 1.8rem; margin-top: 5px;">Portal de Acceso</h1>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert-msg"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Pestañas interactivas para alternar -->
    <div class="auth-tabs">
        <button type="button" class="auth-tab active" onclick="cambiarModo('login')">Iniciar Sesión</button>
        <button type="button" class="auth-tab" onclick="cambiarModo('registro')">Registrarse</button>
    </div>

    <!-- Formulario de Inicio de Sesión -->
    <form id="form-login" class="auth-form active" method="POST">
        <input type="hidden" name="accion" value="login">
        <label>
            Correo electrónico
            <input type="email" name="email" required placeholder="tucorreo@megafest.com">
        </label>
        <label>
            Contraseña
            <input type="password" name="password" required placeholder="••••••••">
        </label>
        <button type="submit" class="auth-btn">Entrar al Panel</button>
    </form>

    <!-- Formulario de Registro (Primera vez) -->
    <form id="form-registro" class="auth-form" method="POST">
        <input type="hidden" name="accion" value="registro">
        <label>
            Correo electrónico nuevo
            <input type="email" name="email" required placeholder="nuevo@megafest.com">
        </label>
        <label>
            Crea una contraseña
            <input type="password" name="password" required placeholder="••••••••">
        </label>
        <button type="submit" class="auth-btn">Crear Cuenta y Entrar</button>
    </form>
</div>

<script>
function cambiarModo(tipo) {
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');
    
    tabs.forEach(t => t.classList.remove('active'));
    forms.forEach(f => f.classList.remove('active'));

    if (tipo === 'login') {
        tabs[0].classList.add('active');
        document.getElementById('form-login').classList.add('active');
    } else {
        tabs[1].classList.add('active');
        document.getElementById('form-registro').classList.add('active');
    }
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
