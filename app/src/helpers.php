<?php
/**
 * Sanitizer – jak miałeś
 */
function safe_html(?string $html): string {
  if ($html === null || $html === '') return '';

  $allowedTags = [
    'a','p','br','strong','em','ul','ol','li','blockquote',
    'h1','h2','h3','h4','h5','h6','span','div','code','pre','hr','img'
  ];
  $allowedAttributes = [
    '*'   => ['class'],
    'a'   => ['href','title','target','rel'],
    'img' => ['src','alt','title','width','height'],
  ];

  $prev = libxml_use_internal_errors(true);
  $doc = new DOMDocument();
  $doc->preserveWhiteSpace = false;
  $doc->loadHTML('<?xml encoding="utf-8"?>'.$html, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);
  libxml_clear_errors();
  libxml_use_internal_errors($prev);

  $sanitize = static function (DOMNode $node) use (&$sanitize,$allowedTags,$allowedAttributes): void {
    if ($node->nodeType === XML_ELEMENT_NODE) {
      $tag = strtolower($node->nodeName);
      if (!in_array($tag, $allowedTags, true)) {
        $parent = $node->parentNode;
        if ($parent) {
          if (in_array($tag, ['script','style'], true)) {
            $parent->removeChild($node);
          } else {
            while ($node->firstChild) $parent->insertBefore($node->firstChild, $node);
            $parent->removeChild($node);
          }
        }
        return;
      }

      $allowedForTag = $allowedAttributes['*'] ?? [];
      if (isset($allowedAttributes[$tag])) {
        $allowedForTag = array_merge($allowedForTag, $allowedAttributes[$tag]);
      }

      if ($node->hasAttributes()) {
        for ($i = $node->attributes->length - 1; $i >= 0; $i--) {
          $attr = $node->attributes->item($i);
          if (!$attr) continue;

          $name  = strtolower($attr->nodeName);
          $value = $attr->nodeValue;

          if (strpos($name, 'on') === 0) { $node->removeAttributeNode($attr); continue; }
          if (!in_array($name, $allowedForTag, true)) { $node->removeAttributeNode($attr); continue; }

          if (in_array($name, ['href','src'], true) && preg_match('/^\s*javascript:/i', $value)) {
            $node->removeAttributeNode($attr); continue;
          }

          if ($name === 'target' && $value === '_blank') {
            $existingRel = $node->getAttribute('rel');
            $requiredRel = 'noopener noreferrer';
            if ($existingRel === '') {
              $node->setAttribute('rel', $requiredRel);
            } else {
              $rels = array_filter(array_map('trim', explode(' ', $existingRel)));
              foreach (explode(' ', $requiredRel) as $part) {
                if (!in_array($part, $rels, true)) $rels[] = $part;
              }
              $node->setAttribute('rel', implode(' ', $rels));
            }
          }
        }
      }
    }
    for ($child = $node->firstChild; $child; $child = $next) {
      $next = $child->nextSibling;
      $sanitize($child);
    }
  };

  for ($child = $doc->firstChild; $child; $child = $child->nextSibling) {
    $sanitize($child);
  }

  $clean = $doc->saveHTML();
  if ($clean === null) return '';
  $clean = preg_replace('/^<\?xml[^>]+>/', '', $clean) ?? $clean;
  return trim($clean);
}

/**
 * Minimalny renderer z twardym logowaniem – usuwa „cichą” pustkę.
 */
function render(string $template, array $vars = [], string $layout = 'layout.php'): void {
  try {
    // diagnostyka błędów na czas renderu
    $oldDisplay = ini_get('display_errors');
    $oldReport  = ini_get('error_reporting');
    ini_set('display_errors', '1');
    error_reporting(E_ALL);

    $tplPath = __DIR__ . '/../templates/' . $template;
    $layPath = __DIR__ . '/../templates/' . $layout;

    if (!file_exists($tplPath)) {
      error_log("[render] template not found: $tplPath");
      http_response_code(500);
      echo "<!-- template not found: $template -->";
      return;
    }
    if (!file_exists($layPath)) {
      error_log("[render] layout not found: $layPath");
      http_response_code(500);
      echo "<!-- layout not found: $layout -->";
      return;
    }

    // zmienne dla template
    extract($vars, EXTR_SKIP);

    // render content
    ob_start();
    include $tplPath;
    $content = ob_get_clean();
    if ($content === false) $content = '';

    $title = $vars['title'] ?? 'Tata Jest Ważny';

    // mały marker żeby curl nie był pusty nawet przy problemie w layout
    echo "<!-- render loaded template=$template -->";

    // render layout (wstrzykuje $content i $title)
    include $layPath;

    // przywróć ustawienia
    ini_set('display_errors', (string)$oldDisplay);
    ini_set('error_reporting', (string)$oldReport);
  } catch (Throwable $e) {
    error_log('[render] FATAL: '.$e->getMessage().' @ '.$e->getFile().':'.$e->getLine());
    http_response_code(500);
    echo "<pre>Render error: ".htmlspecialchars($e->getMessage())."</pre>";
  }
}

/** Prostszą nakładka dla panelu admina */
function render_admin(string $template, array $vars = []): void {
  error_log("[render_admin] template=$template");
  render($template, $vars, 'admin_layout.php');
}

/** Redirect pomocniczy */
function redirect(string $url): never {
  header("Location: $url");
  exit;
}

/** CSRF validator – jak miałeś */
function require_post_csrf(): void {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
    http_response_code(400); echo "Bad CSRF"; exit;
  }
}
