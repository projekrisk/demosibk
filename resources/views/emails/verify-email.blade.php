<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Verifikasi Email SIBK-digi</title>
    <style>
        body { background-color: #f3f4f6; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; }
        table { border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; }
        table td { font-family: sans-serif; font-size: 14px; vertical-align: top; }
        
        .container { display: block; margin: 0 auto !important; max-width: 580px; padding: 10px; width: 580px; }
        .content { box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px; }
        
        .main { background: #ffffff; border-radius: 12px; width: 100%; border: 1px solid #e5e7eb; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .wrapper { box-sizing: border-box; padding: 30px; }
        
        .header { padding: 20px 0; text-align: center; }
        .footer { clear: both; margin-top: 10px; text-align: center; width: 100%; }
        .footer td, .footer p, .footer span, .footer a { color: #9ca3af; font-size: 12px; text-align: center; }

        h1, h2 { color: #111827; font-family: sans-serif; font-weight: 700; line-height: 1.2; margin: 0 0 15px; }
        h1 { font-size: 24px; text-align: center; }
        p { font-family: sans-serif; font-size: 16px; font-weight: normal; margin: 0 0 20px; color: #4b5563; line-height: 1.6; }
        
        .btn { box-sizing: border-box; width: 100%; }
        .btn > tbody > tr > td { padding-bottom: 20px; }
        .btn table { width: auto; margin: 0 auto; }
        .btn table td { background-color: #4f46e5; border-radius: 8px; text-align: center; }
        .btn a { background-color: #4f46e5; border: 1px solid #4f46e5; border-radius: 8px; box-sizing: border-box; color: #ffffff; cursor: pointer; display: inline-block; font-size: 16px; font-weight: bold; margin: 0; padding: 12px 30px; text-decoration: none; }
        
        .fallback-link { font-size: 12px; color: #9ca3af; word-break: break-all; margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .fallback-link a { color: #6b7280; text-decoration: underline; }
    </style>
</head>
<body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
                <div class="content">
                    
                    <div class="header">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center">
                                    <h2 style="color: #4f46e5; margin:0; font-size: 28px;">SIBK-digi</h2>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table role="presentation" class="main">
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <p>Halo, <strong>{{ $user->name }}</strong> 👋</p>
                                            <p>Selamat datang di SIBK-digi! Terima kasih telah mendaftarkan sekolah Anda bersama kami.</p>
                                            <p>Untuk mulai menggunakan layanan dan mengakses dashboard, mohon verifikasi alamat email Anda dengan mengklik tombol di bawah ini:</p>
                                            
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                <tbody>
                                                    <tr>
                                                        <td align="center">
                                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td> <a href="{{ $url }}" target="_blank">Verifikasi Akun Saya</a> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            
                                            <p>Jika Anda tidak merasa melakukan pendaftaran ini, Anda dapat mengabaikan email ini dengan aman.</p>
                                            
                                            <div class="fallback-link">
                                                <p style="font-size: 12px; margin-bottom: 5px;">Jika tombol di atas tidak berfungsi, salin tautan berikut ke browser Anda:</p>
                                                <a href="{{ $url }}">{{ $url }}</a>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block">
                                    <span class="apple-link">SIBK-digi Platform Bimbingan Konseling</span>
                                    <br> &copy; {{ date('Y') }} All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
</body>
</html>