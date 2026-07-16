#!/usr/bin/env python3
"""Download public CSS/fonts/images from pultop.uz into public/."""

from __future__ import annotations

import re
import ssl
import subprocess
import sys
import urllib.error
import urllib.request
from pathlib import Path
from urllib.parse import urljoin, urlparse, unquote

SSL_CTX = ssl._create_unverified_context()

ROOT = Path(__file__).resolve().parents[1]
PUBLIC = ROOT / "public"
UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
BASE = "https://pultop.uz"

# Local destination relative to public/ for each remote CSS (canonical name without query).
CSS_MAP: dict[str, str] = {
    "/wp-content/plugins/contact-form-7/includes/css/styles.css": "css/plugins/cf7-styles.css",
    "/wp-content/plugins/pultopuz/css/style-admin.css": "css/plugins/style-admin.css",
    "/wp-content/plugins/pultopuz/css/pul_style.css": "css/plugins/pul_style.css",
    "/wp-content/plugins/pultopuz/css/jquery.ui.css": "css/plugins/jquery.ui.css",
    "/wp-content/plugins/wp-calc-finance/public/css/jquery.ui.css": "css/plugins/wp-calc-jquery.ui.css",
    "/wp-content/plugins/wp-calc-finance/public/css/style.css": "css/plugins/wp-calc-finance.css",
    "/wp-content/themes/dt-the7/fonts/icomoon-the7-font/icomoon-the7-font.min.css": "fonts/icomoon-the7-font/icomoon-the7-font.min.css",
    "/wp-content/themes/dt-the7/fonts/FontAwesome/css/all.min.css": "fonts/FontAwesome/css/all.min.css",
    "/wp-content/themes/dt-the7/fonts/FontAwesome/back-compat.min.css": "fonts/FontAwesome/back-compat.min.css",
    "/wp-content/uploads/smile_fonts/Defaults/Defaults.css": "fonts/smile_fonts/Defaults/Defaults.css",
    "/wp-content/uploads/smile_fonts/icomoon-icomoonfree-16x16/icomoon-icomoonfree-16x16.css": "fonts/smile_fonts/icomoon-icomoonfree-16x16/icomoon-icomoonfree-16x16.css",
    "/wp-content/plugins/js_composer/assets/css/js_composer.min.css": "css/plugins/js_composer.min.css",
    "/wp-content/themes/dt-the7/css/main.min.css": "css/min.css",
    "/wp-content/themes/dt-the7/lib/custom-scrollbar/custom-scrollbar.min.css": "css/custom-scrollbar.min.css",
    "/wp-content/themes/dt-the7/css/wpbakery.min.css": "css/wpbakery.min.css",
    "/wp-content/plugins/dt-the7-core/assets/css/post-type.min.css": "css/plugins/post-type.min.css",
    "/wp-content/uploads/the7-css/css-vars.css": "css/vars.css",
    "/wp-content/uploads/the7-css/custom.css": "css/custom.css",
    "/wp-content/uploads/the7-css/media.css": "css/media.css",
    "/wp-content/uploads/the7-css/mega-menu.css": "css/mega-menu.css",
    "/wp-content/uploads/the7-css/the7-elements-albums-portfolio.css": "css/the7-elements-albums-portfolio.css",
    "/wp-content/uploads/the7-css/post-type-dynamic.css": "css/post-type-dynamic.css",
    "/wp-content/themes/dt-the7-child/style.css": "css/child-style.css",
    "/wp-content/plugins/Ultimate_VC_Addons/assets/min-css/ultimate.min.css": "css/plugins/ultimate.min.css",
    "/wp-content/plugins/js_composer/assets/lib/bower/animate-css/animate.min.css": "css/plugins/animate.min.css",
    "/wp-content/plugins/pultopuz-shortcode/rate/css/best_rate.css": "css/plugins/best_rate.css",
    "/wp-content/plugins/pultopuz-shortcode/css/style-posts.css": "css/plugins/style-posts.css",
}

EXTRA_IMAGES = [
    "/wp-content/uploads/2026/01/pultop_logo_m.png",
    "/wp-content/uploads/2019/03/pultop-logo_CDR.png",
    "/wp-content/uploads/2026/01/cropped-logo-32x32.png",
    "/wp-content/uploads/2026/01/cropped-logo-180x180.png",
    "/wp-content/uploads/2026/01/cropped-logo-192x192.png",
    "/wp-content/uploads/2026/01/cropped-logo-270x270.png",
    "/favicon-16x16.png",
    "/favicon-32x32.png",
    "/apple-icon-180x180.png",
]

URL_RE = re.compile(
    r"""url\(\s*(['"]?)([^'")]+)\1\s*\)""",
    re.IGNORECASE,
)
INLINE_STYLE_IDS = [
    "wp-custom-css",
    "the7-custom-inline-css",
    "dt-main-inline-css",
]


def fetch(url: str) -> bytes:
    """Prefer curl (reliable CA bundle); fall back to urllib without verify."""
    try:
        result = subprocess.run(
            ["curl", "-fsSLk", "-A", UA, url],
            check=True,
            capture_output=True,
            timeout=90,
        )
        return result.stdout
    except Exception:
        req = urllib.request.Request(url, headers={"User-Agent": UA})
        with urllib.request.urlopen(req, timeout=60, context=SSL_CTX) as resp:
            return resp.read()


def strip_query(path: str) -> str:
    return path.split("?", 1)[0].split("#", 1)[0]


def download_to(url: str, dest: Path) -> bool:
    dest.parent.mkdir(parents=True, exist_ok=True)
    try:
        data = fetch(url)
    except urllib.error.HTTPError as e:
        print(f"  FAIL {e.code} {url}", file=sys.stderr)
        return False
    except Exception as e:  # noqa: BLE001
        print(f"  FAIL {e} {url}", file=sys.stderr)
        return False
    if not data:
        print(f"  FAIL empty {url}", file=sys.stderr)
        return False
    dest.write_bytes(data)
    print(f"  OK {dest.relative_to(PUBLIC)} ({len(data)} bytes)")
    return True


def resolve_asset_dest(css_local: Path, ref: str) -> Path | None:
    """Map a url() reference from a CSS file to a local path under public/."""
    ref = unquote(strip_query(ref.strip()))
    if not ref or ref.startswith("data:"):
        return None
    if ref.startswith("//"):
        ref = "https:" + ref
    if ref.startswith("http://") or ref.startswith("https://"):
        parsed = urlparse(ref)
        if "pultop.uz" not in parsed.netloc:
            return None
        path = parsed.path
        # Prefer preserving theme/plugin relative structure under public/
        if path.startswith("/wp-content/themes/dt-the7/fonts/"):
            return PUBLIC / path.replace("/wp-content/themes/dt-the7/fonts/", "fonts/", 1)
        if path.startswith("/wp-content/uploads/smile_fonts/"):
            return PUBLIC / path.replace("/wp-content/uploads/smile_fonts/", "fonts/smile_fonts/", 1)
        if path.startswith("/wp-content/uploads/"):
            return PUBLIC / "images" / Path(path).name
        if path.startswith("/wp-content/themes/dt-the7/"):
            # images etc.
            rel = path.replace("/wp-content/themes/dt-the7/", "", 1)
            if rel.startswith("images/"):
                return PUBLIC / rel
            return PUBLIC / "vendor/the7" / rel
        if path.startswith("/wp-content/plugins/"):
            return PUBLIC / "vendor" / path.replace("/wp-content/", "", 1)
        return PUBLIC / "images" / Path(path).name

    # Relative to CSS file directory
    abs_local = (css_local.parent / ref).resolve()
    try:
        abs_local.relative_to(PUBLIC.resolve())
    except ValueError:
        # Outside public — put under images
        return PUBLIC / "images" / Path(ref).name
    return abs_local


def extract_urls(css_text: str) -> list[str]:
    return [m.group(2) for m in URL_RE.finditer(css_text)]


def dump_css_and_deps() -> None:
    downloaded: set[str] = set()
    queue: list[tuple[str, Path]] = []

    for remote, local_rel in CSS_MAP.items():
        url = BASE + remote
        dest = PUBLIC / local_rel
        if download_to(url, dest):
            downloaded.add(url.split("?", 1)[0])
            queue.append((url, dest))

    # Recursively fetch url() assets from CSS (and nested CSS if any)
    while queue:
        css_url, css_path = queue.pop(0)
        try:
            text = css_path.read_text(encoding="utf-8", errors="ignore")
        except Exception:
            continue
        for ref in extract_urls(text):
            clean = strip_query(ref.strip())
            if not clean or clean.startswith("data:"):
                continue
            if clean.startswith("//"):
                abs_url = "https:" + clean
            elif clean.startswith("http://") or clean.startswith("https://"):
                abs_url = clean
            else:
                abs_url = urljoin(css_url, clean)

            if "pultop.uz" not in abs_url and not abs_url.startswith(BASE):
                # allow relative that resolved under pultop
                if not abs_url.startswith("https://pultop.uz") and not abs_url.startswith(
                    "http://pultop.uz"
                ):
                    # relative urls already joined with css_url which is on pultop
                    pass

            if not abs_url.startswith("https://pultop.uz") and not abs_url.startswith(
                "http://pultop.uz"
            ):
                continue

            key = abs_url.split("?", 1)[0]
            if key in downloaded:
                continue

            dest = resolve_asset_dest(css_path, ref if not ref.startswith("http") else abs_url)
            # Prefer resolving relative refs against css local path
            if not (ref.startswith("http") or ref.startswith("//")):
                dest = (css_path.parent / strip_query(unquote(ref))).resolve()
                try:
                    dest.relative_to(PUBLIC.resolve())
                except ValueError:
                    dest = PUBLIC / "images" / Path(ref).name

            if dest is None:
                continue

            if download_to(key, dest):
                downloaded.add(key)
                if dest.suffix.lower() == ".css":
                    queue.append((key, dest))


def dump_images() -> None:
    for path in EXTRA_IMAGES:
        url = BASE + path
        name = Path(path).name
        if path.startswith("/favicon") or "cropped-logo" in path or path.startswith("/apple-"):
            dest = PUBLIC / "favicon" / name
        else:
            dest = PUBLIC / "images" / name
        download_to(url, dest)


def dump_inline_from_homepage() -> None:
    html = fetch(BASE + "/").decode("utf-8", errors="ignore")
    for style_id in INLINE_STYLE_IDS:
        m = re.search(
            rf'<style[^>]*id=[\'"]{re.escape(style_id)}[\'"][^>]*>(.*?)</style>',
            html,
            re.S | re.I,
        )
        if not m:
            print(f"  MISS inline {style_id}", file=sys.stderr)
            continue
        dest = PUBLIC / "css" / "inline" / f"{style_id}.css"
        dest.parent.mkdir(parents=True, exist_ok=True)
        dest.write_text(m.group(1).strip() + "\n", encoding="utf-8")
        print(f"  OK {dest.relative_to(PUBLIC)} ({len(m.group(1))} chars)")


def main() -> int:
    print("== CSS + font deps ==")
    dump_css_and_deps()
    print("== Logos / favicons ==")
    dump_images()
    print("== Inline styles ==")
    dump_inline_from_homepage()
    print("Done.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
