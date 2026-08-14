<?php
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/SmtpMailer.php';

class EmailService {

    private function mailer(): SmtpMailer {
        return new SmtpMailer(MAIL_HOST, MAIL_PORT, MAIL_SECURE, MAIL_USER, MAIL_PASS, MAIL_FROM_EMAIL, MAIL_FROM_NAME);
    }

    private function wrap(string $c): string {
        $s = SCHOOL_NAME; $y = date('Y');
        return "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'></head>
        <body style='margin:0;padding:0;background:#f0f4f9;font-family:Arial,sans-serif'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background:#f0f4f9;padding:32px 0'>
          <tr><td align='center'>
          <table width='560' cellpadding='0' cellspacing='0' style='max-width:560px;width:100%'>
            <tr><td style='background:linear-gradient(135deg,#0077ff,#0044bb);border-radius:14px 14px 0 0;padding:22px 32px'>
              <div style='color:#fff;font-size:17px;font-weight:700'>Mesa de Ayuda</div>
              <div style='color:rgba(255,255,255,.7);font-size:11px;text-transform:uppercase;letter-spacing:.1em'>$s</div>
            </td></tr>
            <tr><td style='background:#fff;padding:28px 32px;border:1px solid #dde4ef;border-top:none'>$c</td></tr>
            <tr><td style='background:#f7faff;border:1px solid #dde4ef;border-top:none;border-radius:0 0 14px 14px;padding:14px 32px;text-align:center'>
              <p style='margin:0;color:#9aafca;font-size:11px'>Correo automático © $y $s</p>
            </td></tr>
          </table></td></tr>
        </table></body></html>";
    }

    public function notificarTicketCreado(array $t, array $u): bool {
        try {
            $n  = htmlspecialchars($u['nombre']);
            $f  = htmlspecialchars($t['folio']);
            $ti = htmlspecialchars($t['titulo']);
            $url = APP_URL . '/index.php?c=tickets&a=detail&id=' . $t['id'];
            $c = "<h2 style='margin:0 0 8px;color:#0d1b2a;font-size:19px;font-weight:800'>¡Hola, $n!</h2>
            <p style='margin:0 0 16px;color:#6b7c93;font-size:14px'>Tu ticket fue registrado exitosamente.</p>
            <div style='background:#f7faff;border:1px solid #dde4ef;border-left:4px solid #0077ff;border-radius:10px;padding:14px 16px;margin-bottom:18px'>
              <div style='font-family:monospace;font-weight:800;color:#0044bb;font-size:13px'>$f</div>
              <div style='font-size:14px;font-weight:600;color:#0d1b2a;margin-top:4px'>$ti</div>
            </div>
            <div style='text-align:center'><a href='$url' style='background:#0077ff;color:#fff;text-decoration:none;padding:10px 24px;border-radius:9px;font-size:14px;font-weight:700;display:inline-block'>Ver mi ticket</a></div>";
            $this->mailer()->send($u['email'], $u['nombre'].' '.$u['apellido'], '['.$t['folio'].'] Ticket registrado — '.$t['titulo'], $this->wrap($c));
            return true;
        } catch (RuntimeException $e) { error_log('Email: '.$e->getMessage()); return false; }
    }

    public function notificarAdmins(array $t, array $u, array $admins): void {
        foreach ($admins as $a) {
            try {
                $na  = htmlspecialchars($a['nombre']);
                $f   = htmlspecialchars($t['folio']);
                $ti  = htmlspecialchars($t['titulo']);
                $sol = htmlspecialchars($u['nombre'].' '.$u['apellido']);
                $url = APP_URL.'/index.php?c=tickets&a=detail&id='.$t['id'];
                $c = "<div style='background:#fff3cd;border:1px solid #ffe58a;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#856404'><strong>⚠ Nuevo ticket sin asignar</strong></div>
                <h2 style='margin:0 0 6px;color:#0d1b2a;font-size:18px;font-weight:800'>Hola, $na</h2>
                <p style='margin:0 0 14px;color:#6b7c93;font-size:14px'>Nuevo ticket de <strong>$sol</strong>.</p>
                <div style='background:#f7faff;border:1px solid #dde4ef;border-left:4px solid #0077ff;border-radius:10px;padding:14px 16px;margin-bottom:16px'>
                  <div style='font-family:monospace;font-weight:800;color:#0044bb'>$f</div>
                  <div style='font-size:14px;font-weight:600;color:#0d1b2a;margin-top:4px'>$ti</div>
                </div>
                <div style='text-align:center'><a href='$url' style='background:#0077ff;color:#fff;text-decoration:none;padding:10px 24px;border-radius:9px;font-size:14px;font-weight:700;display:inline-block'>Atender ticket</a></div>";
                $this->mailer()->send($a['email'], $a['nombre'].' '.$a['apellido'], '[NUEVO] '.$t['folio'].' — '.$t['titulo'], $this->wrap($c));
            } catch (RuntimeException $e) { error_log('Email: '.$e->getMessage()); }
        }
    }

    public function notificarCambioEstado(array $t, array $u, string $ant, string $nuevo): bool {
        try {
            $n   = htmlspecialchars($u['nombre']);
            $f   = htmlspecialchars($t['folio']);
            $ti  = htmlspecialchars($t['titulo']);
            $url = APP_URL.'/index.php?c=tickets&a=detail&id='.$t['id'];
            $res = $t['resolucion'] ? '<div style="background:#d4edda;border-radius:8px;padding:12px 14px;margin:12px 0;font-size:13px;color:#155724"><strong>✅ Resolución:</strong><br>'.nl2br(htmlspecialchars($t['resolucion'])).'</div>' : '';
            $c = "<h2 style='margin:0 0 6px;color:#0d1b2a;font-size:18px;font-weight:800'>Hola, $n</h2>
            <p style='margin:0 0 14px;color:#6b7c93;font-size:14px'>Tu ticket ha sido actualizado.</p>
            <div style='background:#f7faff;border:1px solid #dde4ef;border-left:4px solid #0077ff;border-radius:10px;padding:14px 16px;margin-bottom:12px'>
              <div style='font-family:monospace;font-weight:800;color:#0044bb'>$f</div>
              <div style='font-size:14px;font-weight:600;color:#0d1b2a;margin-top:4px'>$ti</div>
            </div>
            <div style='font-size:13px;color:#6b7c93'>Estado: <strong style='color:#0d1b2a'>$ant</strong> → <strong style='color:#0077ff'>$nuevo</strong></div>
            $res
            <div style='text-align:center;margin-top:16px'><a href='$url' style='background:#0077ff;color:#fff;text-decoration:none;padding:10px 24px;border-radius:9px;font-size:14px;font-weight:700;display:inline-block'>Ver ticket</a></div>";
            $this->mailer()->send($u['email'], $u['nombre'].' '.$u['apellido'], '['.$t['folio'].'] Actualización de tu ticket', $this->wrap($c));
            return true;
        } catch (RuntimeException $e) { error_log('Email: '.$e->getMessage()); return false; }
    }

    public function notificarMantenimiento(array $m, array $specs = []): bool {
        try {
            $n   = htmlspecialchars($m['nombre_tecnico']);
            $f   = htmlspecialchars($m['folio']);
            $tip = $m['tipo'] === 'preventivo' ? 'Preventivo' : 'Correctivo';
            $fec = date('d/m/Y', strtotime($m['fecha_programada']));
            $url = $m['url_publica'] ?? (APP_URL . '/index.php?c=mantenimiento&a=detail&id=' . $m['id']);

            $filasHtml   = $this->buildEquipoTabla($m);
            $specsHtml   = $this->buildSpecsTabla($specs);
            $seccionSpecs = $specsHtml ? "<div style='font-size:11px;font-weight:700;color:#9aafca;text-transform:uppercase;letter-spacing:.08em;margin:14px 0 6px'>Especificaciones técnicas</div><table style='width:100%;border-collapse:collapse'>$specsHtml</table>" : '';
            $desc = $m['descripcion'] ? "<div style='font-size:11px;font-weight:700;color:#9aafca;text-transform:uppercase;letter-spacing:.08em;margin:14px 0 6px'>Actividades a realizar</div><div style='background:#fff3cd;border:1px solid #ffe58a;border-radius:8px;padding:10px 14px;font-size:13px;line-height:1.6'>" . nl2br(htmlspecialchars($m['descripcion'])) . "</div>" : '';

            $c = "<h2 style='margin:0 0 6px;color:#0d1b2a;font-size:18px;font-weight:800'>Hola, $n</h2>
            <p style='margin:0 0 14px;color:#6b7c93;font-size:14px'>Se ha programado un mantenimiento <strong>$tip</strong> para tu equipo el día <strong>$fec</strong>.</p>
            <div style='background:#f7faff;border:1px solid #dde4ef;border-left:4px solid #0077ff;border-radius:10px;padding:12px 16px;margin-bottom:14px'>
                <div style='font-family:monospace;font-weight:800;color:#0044bb;font-size:13px'>$f</div>
                <div style='font-size:12px;color:#6b7c93;margin-top:2px'>Mantenimiento $tip · $fec</div>
            </div>
            <div style='font-size:11px;font-weight:700;color:#9aafca;text-transform:uppercase;letter-spacing:.08em;margin:0 0 6px'>Equipo</div>
            <table style='width:100%;border-collapse:collapse'>$filasHtml</table>
            $seccionSpecs $desc
            <div style='text-align:center;margin-top:20px'>
                <a href='$url' style='background:#0077ff;color:#fff;text-decoration:none;padding:11px 28px;border-radius:9px;font-size:14px;font-weight:700;display:inline-block'>Ver orden de mantenimiento</a>
            </div>
            <p style='text-align:center;margin-top:10px;font-size:11px;color:#9aafca'>Este enlace es exclusivo para ti y no requiere contraseña.</p>";

            $this->mailer()->send($m['email_tecnico'], $m['nombre_tecnico'], '[' . $f . '] Mantenimiento ' . $tip . ' — ' . $m['equipo_codigo'], $this->wrap($c));
            return true;
        } catch (RuntimeException $e) { error_log('Email mantenimiento: '.$e->getMessage()); return false; }
    }

    public function notificarCambioEstadoMantenimiento(array $m, array $specs = [], string $estado = ''): bool {
        try {
            $n   = htmlspecialchars($m['nombre_tecnico']);
            $f   = htmlspecialchars($m['folio']);
            $tip = $m['tipo'] === 'preventivo' ? 'Preventivo' : 'Correctivo';
            $url = $m['url_publica'] ?? (APP_URL . '/index.php?c=mantenimiento&a=publico&token=' . $m['token_acceso']);

            $esCompletado = $estado === 'completado';
            $color  = $esCompletado ? '#198754' : '#c62828';
            $bg     = $esCompletado ? '#d4edda'  : '#fce4ec';
            $icono  = $esCompletado ? '✅' : '❌';
            $titulo = $esCompletado ? 'Mantenimiento completado' : 'Mantenimiento cancelado';
            $msg    = $esCompletado
                ? 'El mantenimiento de tu equipo ha sido <strong>completado</strong> satisfactoriamente.'
                : 'El mantenimiento de tu equipo ha sido <strong>cancelado</strong>.';

            $filasHtml = $this->buildEquipoTabla($m);
            $notas     = $m['notas'] ? "<div style='background:#f7faff;border:1px solid #dde4ef;border-radius:8px;padding:10px 14px;margin-top:12px;font-size:13px;line-height:1.6'><strong>Notas:</strong><br>" . nl2br(htmlspecialchars($m['notas'])) . "</div>" : '';

            $c = "<div style='background:$bg;border-radius:10px;padding:12px 16px;margin-bottom:16px;text-align:center'>
                <div style='font-size:22px;margin-bottom:4px'>$icono</div>
                <div style='font-weight:800;color:$color;font-size:15px'>$titulo</div>
            </div>
            <h2 style='margin:0 0 6px;color:#0d1b2a;font-size:18px;font-weight:800'>Hola, $n</h2>
            <p style='margin:0 0 14px;color:#6b7c93;font-size:14px'>$msg</p>
            <div style='background:#f7faff;border:1px solid #dde4ef;border-left:4px solid $color;border-radius:10px;padding:12px 16px;margin-bottom:14px'>
                <div style='font-family:monospace;font-weight:800;color:#0044bb;font-size:13px'>$f</div>
                <div style='font-size:12px;color:#6b7c93;margin-top:2px'>Mantenimiento $tip</div>
            </div>
            <div style='font-size:11px;font-weight:700;color:#9aafca;text-transform:uppercase;letter-spacing:.08em;margin:0 0 6px'>Equipo</div>
            <table style='width:100%;border-collapse:collapse'>$filasHtml</table>
            $notas
            <div style='text-align:center;margin-top:20px'>
                <a href='$url' style='background:#0077ff;color:#fff;text-decoration:none;padding:11px 28px;border-radius:9px;font-size:14px;font-weight:700;display:inline-block'>Ver orden completa</a>
            </div>
            <p style='text-align:center;margin-top:10px;font-size:11px;color:#9aafca'>Este enlace es exclusivo para ti y no requiere contraseña.</p>";

            $asunto = '[' . $f . '] ' . $titulo . ' — ' . $m['equipo_codigo'];
            $this->mailer()->send($m['email_tecnico'], $m['nombre_tecnico'], $asunto, $this->wrap($c));
            return true;
        } catch (RuntimeException $e) { error_log('Email cambio estado mant: '.$e->getMessage()); return false; }
    }

    public function notificarAsignacionEquipo(array $a, array $specs = []): bool {
        try {
            if (empty($a['email_usuario'])) return false;

            $roleLabels = [
                'auxiliar_administrativo' => 'Auxiliar Administrativo',
                'coordinador'             => 'Coordinador',
                'operario'                => 'Operario',
                'ayudante'                => 'Ayudante',
                'residente_becario'       => 'Residente/Becario',
                'auxiliar_seguridad'      => 'Auxiliar de Seguridad',
                'auxiliar_oficina'        => 'Auxiliar de Oficina',
                'control_de_obra'         => 'Control de Obra',
                'supervisor_seguridad'    => 'Supervisor de Seguridad',
                'contra_incendios'        => 'Contra Incendios',
                'tecnico_instrumentista'  => 'Técnico Instrumentista',
                'admin'                   => 'Administrador',
                'tecnico'                 => 'Técnico',
                'maestro'                 => 'Maestro',
            ];

            $nombre = htmlspecialchars($a['nombre_usuario']);
            $folio  = htmlspecialchars($a['folio']);
            $codigo = htmlspecialchars($a['equipo_codigo']);
            $fecha  = !empty($a['fecha_asignacion']) ? date('d/m/Y', strtotime($a['fecha_asignacion'])) : date('d/m/Y');
            $puesto = htmlspecialchars($roleLabels[$a['rol_usuario'] ?? ''] ?? ucwords(str_replace('_', ' ', $a['rol_usuario'] ?? '')));
            $depto  = htmlspecialchars($a['dept_usuario'] ?? '—');
            $admin  = htmlspecialchars($a['nombre_admin'] ?? 'TI');
            $devolucion = !empty($a['fecha_devolucion_esperada']) ? date('d/m/Y', strtotime($a['fecha_devolucion_esperada'])) : 'No definida';

            $filasEquipo = $this->buildEquipoTabla($a);
            $specsHtml   = $this->buildSpecsTabla($specs);
            $seccionSpecs = $specsHtml ? "<div style='font-size:11px;font-weight:700;color:#9aafca;text-transform:uppercase;letter-spacing:.08em;margin:14px 0 6px'>Especificaciones técnicas</div><table style='width:100%;border-collapse:collapse'>$specsHtml</table>" : '';

            $obra = !empty($a['nombre_obra'])
                ? "<tr><td style='padding:5px 10px;font-weight:700;font-size:12px;color:#6b7c93;background:#f7faff;border:1px solid #dde4ef;width:35%'>Obra</td><td style='padding:5px 10px;font-size:12px;border:1px solid #dde4ef'>" . htmlspecialchars($a['nombre_obra']) . "</td></tr>"
                : '';
            $contrato = !empty($a['numero_contrato'])
                ? "<tr><td style='padding:5px 10px;font-weight:700;font-size:12px;color:#6b7c93;background:#f7faff;border:1px solid #dde4ef;width:35%'>Contrato</td><td style='padding:5px 10px;font-size:12px;border:1px solid #dde4ef'>" . htmlspecialchars($a['numero_contrato']) . "</td></tr>"
                : '';
            $notas = !empty($a['notas_entrega'])
                ? "<div style='background:#fff3cd;border:1px solid #ffe58a;border-radius:8px;padding:10px 14px;margin-top:14px;font-size:13px;line-height:1.6'><strong>Notas de entrega:</strong><br>" . nl2br(htmlspecialchars($a['notas_entrega'])) . "</div>"
                : '';

            $contenido = "<h2 style='margin:0 0 6px;color:#0d1b2a;font-size:18px;font-weight:800'>Hola, $nombre</h2>
            <p style='margin:0 0 14px;color:#6b7c93;font-size:14px'>Se te ha asignado un equipo de cómputo. Te compartimos los datos de la asignación.</p>
            <div style='background:#f7faff;border:1px solid #dde4ef;border-left:4px solid #0077ff;border-radius:10px;padding:12px 16px;margin-bottom:14px'>
                <div style='font-family:monospace;font-weight:800;color:#0044bb;font-size:13px'>$folio</div>
                <div style='font-size:12px;color:#6b7c93;margin-top:2px'>Equipo asignado: <strong>$codigo</strong> · $fecha</div>
            </div>
            <div style='font-size:11px;font-weight:700;color:#9aafca;text-transform:uppercase;letter-spacing:.08em;margin:0 0 6px'>Datos del usuario</div>
            <table style='width:100%;border-collapse:collapse;margin-bottom:14px'>
              <tr><td style='padding:5px 10px;font-weight:700;font-size:12px;color:#6b7c93;background:#f7faff;border:1px solid #dde4ef;width:35%'>Nombre</td><td style='padding:5px 10px;font-size:12px;border:1px solid #dde4ef'>$nombre</td></tr>
              <tr><td style='padding:5px 10px;font-weight:700;font-size:12px;color:#6b7c93;background:#f7faff;border:1px solid #dde4ef;width:35%'>Puesto / Rol</td><td style='padding:5px 10px;font-size:12px;border:1px solid #dde4ef'>$puesto</td></tr>
              <tr><td style='padding:5px 10px;font-weight:700;font-size:12px;color:#6b7c93;background:#f7faff;border:1px solid #dde4ef;width:35%'>Departamento</td><td style='padding:5px 10px;font-size:12px;border:1px solid #dde4ef'>$depto</td></tr>
            </table>
            <div style='font-size:11px;font-weight:700;color:#9aafca;text-transform:uppercase;letter-spacing:.08em;margin:0 0 6px'>Equipo asignado</div>
            <table style='width:100%;border-collapse:collapse'>$filasEquipo</table>
            $seccionSpecs
            <div style='font-size:11px;font-weight:700;color:#9aafca;text-transform:uppercase;letter-spacing:.08em;margin:14px 0 6px'>Datos de asignación</div>
            <table style='width:100%;border-collapse:collapse'>
              <tr><td style='padding:5px 10px;font-weight:700;font-size:12px;color:#6b7c93;background:#f7faff;border:1px solid #dde4ef;width:35%'>Fecha de asignación</td><td style='padding:5px 10px;font-size:12px;border:1px solid #dde4ef'>$fecha</td></tr>
              <tr><td style='padding:5px 10px;font-weight:700;font-size:12px;color:#6b7c93;background:#f7faff;border:1px solid #dde4ef;width:35%'>Devolución esperada</td><td style='padding:5px 10px;font-size:12px;border:1px solid #dde4ef'>$devolucion</td></tr>
              <tr><td style='padding:5px 10px;font-weight:700;font-size:12px;color:#6b7c93;background:#f7faff;border:1px solid #dde4ef;width:35%'>Entregado por</td><td style='padding:5px 10px;font-size:12px;border:1px solid #dde4ef'>$admin</td></tr>
              $obra
              $contrato
            </table>
            $notas
            <p style='margin:16px 0 0;color:#6b7c93;font-size:12px;line-height:1.5'>Por favor conserva este correo como comprobante de la asignación y reporta cualquier detalle del equipo al área de TI.</p>";

            $this->mailer()->send(
                $a['email_usuario'],
                $a['nombre_usuario'],
                '[' . $a['folio'] . '] Asignación de equipo ' . $a['equipo_codigo'],
                $this->wrap($contenido)
            );
            return true;
        } catch (RuntimeException $e) {
            error_log('Email asignacion: ' . $e->getMessage());
            return false;
        }
    }

    // ── Helpers privados ──────────────────────────────────────────────────
    private function buildEquipoTabla(array $m): string {
        $filas = [
            ['Código',    $m['equipo_codigo']],
            ['Categoría', $m['categoria_nombre']],
            ['Marca',     $m['equipo_marca']     ?? '—'],
            ['Modelo',    $m['equipo_modelo']    ?? '—'],
            ['No. Serie', $m['equipo_serie']     ?? '—'],
            ['Ubicación', $m['equipo_ubicacion'] ?? '—'],
        ];
        $html = '';
        foreach ($filas as [$k, $v]) {
            $html .= "<tr>
                <td style='padding:5px 10px;font-weight:700;font-size:12px;color:#6b7c93;background:#f7faff;border:1px solid #dde4ef;width:35%'>" . htmlspecialchars($k) . "</td>
                <td style='padding:5px 10px;font-size:12px;border:1px solid #dde4ef'>" . htmlspecialchars($v) . "</td>
            </tr>";
        }
        return $html;
    }

    private function buildSpecsTabla(array $specs): string {
        $omitir = ['direccion_mac','direccion_ip','usuario_pc','contrasena_pc'];
        $html   = '';
        foreach ($specs as $s) {
            if (!$s['valor'] || in_array($s['nombre_campo'], $omitir)) continue;
            $html .= "<tr>
                <td style='padding:5px 10px;font-weight:700;font-size:12px;color:#6b7c93;background:#f7faff;border:1px solid #dde4ef;width:35%'>" . htmlspecialchars($s['etiqueta']) . "</td>
                <td style='padding:5px 10px;font-size:12px;border:1px solid #dde4ef'>" . htmlspecialchars($s['valor']) . "</td>
            </tr>";
        }
        return $html;
    }
}
