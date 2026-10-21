# PdfParser v2.13.1

Fuente: https://github.com/smalot/pdfparser/releases/tag/v2.13.1
Licencia: LGPL-3.0, texto completo en LICENSE.txt. Código fuente distribuido sin
modificaciones en src/. Se conserva separado y reemplazable mediante su cargador
en bootstrap/pdfparser.php. No se ejecuta Composer en producción.

SHA-256 del archivo descargado de GitHub para esta incorporación:
3b08be1574e0853689cbddf8cad7cde7165b5098e8f39f49a1e98bbd917cb468.

Requiere PHP, zlib, iconv y funciones mbstring. Se utiliza la extensión mbstring
nativa que ya requiere la aplicación; no es necesario el polyfill de Symfony.
La aplicación limita tamaño, páginas, anidamiento y decodificación, no ejecuta
acciones del PDF ni realiza OCR o peticiones a URL contenidas en el documento.
La distribución de esta biblioteca no incluye documentos de clientes.
