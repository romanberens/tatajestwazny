<?php
function layout(string $title, string $bodyHtml): void {
  echo "<!DOCTYPE html>
  <html lang='pl'>
  <head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>" . htmlspecialchars($title) . "</title>
    <link rel='manifest' href='/manifest.webmanifest'>
    <script src='/sw.js' defer></script>
    <style>
      body { font-family: sans-serif; max-width: 800px; margin: 2em auto; padding: 1em; line-height: 1.6; }
      h1, h2, h3 { color: #333; }
      a { color: #007acc; text-decoration: none; }
      a:hover { text-decoration: underline; }
    </style>
  </head>
  <body>
    $bodyHtml
  </body>
  </html>";
}
