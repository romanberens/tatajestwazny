<?php
function safe_html(?string $html): string {
  if ($html === null || $html === '') {
    return '';
  }

  $allowedTags = [
    'a', 'p', 'br', 'strong', 'em', 'ul', 'ol', 'li', 'blockquote',
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'div', 'code', 'pre', 'hr', 'img'
  ];
  $allowedAttributes = [
    '*' => ['class'],
    'a' => ['href', 'title', 'target', 'rel'],
    'img' => ['src', 'alt', 'title', 'width', 'height'],
  ];

  $previousLibxmlUseInternalErrors = libxml_use_internal_errors(true);
  $doc = new DOMDocument();
  $doc->preserveWhiteSpace = false;
  $options = LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD;
  $doc->loadHTML('<?xml encoding="utf-8"?>' . $html, $options);
  libxml_clear_errors();
  libxml_use_internal_errors($previousLibxmlUseInternalErrors);

  $sanitizeNode = static function (DOMNode $node) use (&$sanitizeNode, $allowedTags, $allowedAttributes): void {
    if ($node->nodeType === XML_ELEMENT_NODE) {
      $tag = strtolower($node->nodeName);
      if (!in_array($tag, $allowedTags, true)) {
        $parent = $node->parentNode;
        if ($parent) {
          if (in_array($tag, ['script', 'style'], true)) {
            $parent->removeChild($node);
          } else {
            while ($node->firstChild) {
              $parent->insertBefore($node->firstChild, $node);
            }
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
          $attribute = $node->attributes->item($i);
          if (!$attribute) {
            continue;
          }

          $name = strtolower($attribute->nodeName);
          $value = $attribute->nodeValue;

          if (strpos($name, 'on') === 0) {
            $node->removeAttributeNode($attribute);
            continue;
          }

          if (!in_array($name, $allowedForTag, true)) {
            $node->removeAttributeNode($attribute);
            continue;
          }

          if (in_array($name, ['href', 'src'], true)) {
            if (preg_match('/^\s*javascript:/i', $value)) {
              $node->removeAttributeNode($attribute);
              continue;
            }
          }

          if ($name === 'target' && $value === '_blank') {
            $existingRel = $node->getAttribute('rel');
            $requiredRel = 'noopener noreferrer';
            if ($existingRel === '') {
              $node->setAttribute('rel', $requiredRel);
            } else {
              $rels = array_filter(array_map('trim', explode(' ', $existingRel)));
              foreach (explode(' ', $requiredRel) as $relPart) {
                if (!in_array($relPart, $rels, true)) {
                  $rels[] = $relPart;
                }
              }
              $node->setAttribute('rel', implode(' ', $rels));
            }
          }
        }
      }
    }

    for ($child = $node->firstChild; $child; $child = $next) {
      $next = $child->nextSibling;
      $sanitizeNode($child);
    }
  };

  for ($child = $doc->firstChild; $child; $child = $child->nextSibling) {
    $sanitizeNode($child);
  }

  $cleanHtml = $doc->saveHTML();

  if ($cleanHtml === null) {
    return '';
  }

  $cleanHtml = preg_replace('/^<\?xml[^>]+>/', '', $cleanHtml) ?? $cleanHtml;

  return trim($cleanHtml);
}

function render(string $template, array $vars = [], string $layout='layout.php'): void {
  extract($vars, EXTR_SKIP);
  ob_start(); include __DIR__ . '/../templates/' . $template; $content = ob_get_clean();
  $title = $vars['title'] ?? 'Tata Jest Ważny';
  include __DIR__ . '/../templates/' . $layout;
}
function render_admin(string $template, array $vars=[]): void {
  render($template, $vars, 'admin_layout.php');
}
function redirect(string $url): never { header("Location: $url"); exit; }
function require_post_csrf(): void {
  if ($_SERVER['REQUEST_METHOD']!=='POST' || empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
    http_response_code(400); echo "Bad CSRF"; exit;
  }
}
