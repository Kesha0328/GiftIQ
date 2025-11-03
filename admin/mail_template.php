<?php

function giftIQMailTemplate($title, $content) {
  return '
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . '</title>

    <style>
      :root {
        color-scheme: light dark;
        supported-color-schemes: light dark;
      }

      /* Light mode (default) */
      body {
        background-color: #f5f5f5;
        color: #222;
        font-family: Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        margin: 0;
        padding: 0;
      }

      .container {
        max-width: 600px;
        margin: auto;
        background: #ffffff;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #ddd;
      }

      .header {
        background: #fff4eb;
        padding: 25px 20px;
        text-align: center;
      }

      .header img {
        width: 120px;
        height: auto;
        display: block;
        margin: 0 auto 5px;
      }

      .header h2 {
        color: #c87941;
        margin: 10px 0 0;
        font-weight: 700;
      }

      .content {
        padding: 25px;
        color: #333;
        background: #fff;
      }

      .content h3 {
        color: #c87941;
        margin-top: 0;
      }

      .footer {
        background: #f2f2f2;
        color: #666;
        text-align: center;
        padding: 15px;
        font-size: 13px;
      }

      .footer strong {
        color: #c87941;
      }

      /* Dark Mode */
      @media (prefers-color-scheme: dark) {
        body {
          background-color: #0e0e0e !important;
          color: #ddd !important;
        }
        .container {
          background: #181818 !important;
          border-color: #333 !important;
        }
        .header {
          background: #111 !important;
        }
        .header h2 {
          color: #f7b47d !important;
        }
        .content {
          background: #1d1d1d !important;
          color: #ddd !important;
        }
        .content h3 {
          color: #f7b47d !important;
        }
        .footer {
          background: #111 !important;
          color: #aaa !important;
        }
        .footer strong {
          color: #f7b47d !important;
        }
      }

      @media only screen and (max-width:600px) {
        .container {
          width: 100% !important;
          padding: 15px !important;
        }
      }
    </style>
  </head>

  <body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td align="center" style="padding:20px;">
          <table class="container" role="presentation" cellspacing="0" cellpadding="0" width="600">
            
            <!-- Header -->
            <tr>
              <td class="header">
                <img src="https://i.ibb.co/WpnJ75PB/logo.png" alt="GiftIQ Logo">
                <h2>GiftIQ</h2>
              </td>
            </tr>

            <!-- Body -->
            <tr>
              <td class="content">
                <h3>' . htmlspecialchars($title) . '</h3>
                <div style="font-size:15px;line-height:1.6;">' . $content . '</div>
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <td class="footer">
                <p style="margin:0;">With ❤️ from <strong>Team GiftIQ</strong></p>
                <p style="margin:0;">© ' . date('Y') . ' GiftIQ. All rights reserved.</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
  </html>';
}
?>
