<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Restablecimiento de contraseña</title>
    <style>
        /* Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f0f4f0;
            color: #2d3748;
            -webkit-font-smoothing: antialiased;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f0f4f0;
            padding: 40px 16px;
        }

        .email-container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #064e3b 0%, #022c22 100%);
            padding: 40px 40px 32px;
            text-align: center;
        }

        .email-header img {
            max-height: 70px;
            width: auto;
        }

        .header-badge {
            display: inline-block;
            margin-top: 20px;
            background: rgba(52, 211, 153, 0.15);
            border: 1px solid rgba(52, 211, 153, 0.3);
            border-radius: 100px;
            padding: 6px 16px;
            color: #6ee7b7;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Body */
        .email-body {
            padding: 40px 40px 32px;
        }

        .greeting {
            font-size: 22px;
            font-weight: 700;
            color: #064e3b;
            margin-bottom: 12px;
        }

        .body-text {
            font-size: 15px;
            line-height: 1.7;
            color: #4a5568;
            margin-bottom: 16px;
        }

        /* CTA Button */
        .cta-wrapper {
            text-align: center;
            margin: 32px 0;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #439229 0%, #367524 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            padding: 16px 40px;
            border-radius: 12px;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 15px rgba(67, 146, 41, 0.4);
        }

        /* Expiry notice */
        .expiry-notice {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #92400e;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .expiry-icon {
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* URL fallback */
        .url-fallback {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            word-break: break-all;
            font-size: 12px;
            color: #718096;
            font-family: monospace;
            margin-bottom: 24px;
        }

        /* Security notice */
        .security-notice {
            font-size: 13px;
            color: #718096;
            line-height: 1.6;
            padding-top: 16px;
            border-top: 1px solid #edf2f7;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: #edf2f7;
            margin: 28px 0;
        }

        /* Footer */
        .email-footer {
            background: #064e3b;
            padding: 28px 40px;
            text-align: center;
        }

        .footer-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.7;
        }

        .footer-brand {
            font-size: 13px;
            font-weight: 600;
            color: #6ee7b7;
            margin-bottom: 6px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-body { padding: 28px 24px; }
            .email-header { padding: 28px 24px; }
            .email-footer { padding: 20px 24px; }
            .cta-button { padding: 14px 28px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">

            {{-- Header --}}
            <div class="email-header">
                <img src="{{ asset('images/logo-alt.png') }}" alt="Suministros Sustentables" onerror="this.style.display='none'">
                <div>
                    <span class="header-badge">🔐 Seguridad de cuenta</span>
                </div>
            </div>

            {{-- Body --}}
            <div class="email-body">

                <p class="greeting">Hola, {{ $nombreContacto }} 👋</p>

                <p class="body-text">
                    Recibimos una solicitud para restablecer la contraseña de tu cuenta en el
                    <strong>Portal de Clientes de Suministros Sustentables</strong>.
                </p>

                <p class="body-text">
                    Haz clic en el siguiente botón para crear una nueva contraseña:
                </p>

                <div class="cta-wrapper">
                    <a href="{{ $resetUrl }}" class="cta-button" target="_blank">
                        Restablecer mi contraseña
                    </a>
                </div>

                {{-- Aviso de expiración --}}
                <div class="expiry-notice">
                    <span class="expiry-icon">⏰</span>
                    <span>
                        Este enlace expirará en <strong>{{ $expiresIn }} minutos</strong> a partir de la recepción de este correo.
                        Si ya expiró, puedes solicitar uno nuevo desde la pantalla de inicio de sesión.
                    </span>
                </div>

                {{-- URL alternativa --}}
                <p style="font-size: 13px; color: #718096; margin-bottom: 8px;">
                    Si el botón no funciona, copia y pega este enlace en tu navegador:
                </p>
                <div class="url-fallback">{{ $resetUrl }}</div>

                <div class="divider"></div>

                {{-- Aviso de seguridad --}}
                <p class="security-notice">
                    🛡️ <strong>¿No solicitaste este cambio?</strong><br>
                    Si no realizaste esta solicitud, ignora este correo. Tu contraseña no será modificada
                    y tu cuenta permanecerá segura. Si tienes dudas, contacta a tu asesor de ventas.
                </p>

            </div>

            {{-- Footer --}}
            <div class="email-footer">
                <p class="footer-brand">Portal SUCCESS Suministros Sustentables</p>
                <p class="footer-text">
                    Este es un correo automático, por favor no respondas directamente a este mensaje.<br>
                    &copy; {{ date('Y') }} Suministros Sustentables. Todos los derechos reservados.
                </p>
            </div>

        </div>
    </div>
</body>
</html>
