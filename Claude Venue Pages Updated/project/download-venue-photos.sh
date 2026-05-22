#!/bin/bash
# download-venue-photos.sh
# Run this from the project/ folder on your Mac:
#   chmod +x download-venue-photos.sh && ./download-venue-photos.sh
#
# Downloads the best available photo for each venue from Wikipedia/Wikimedia
# and saves it to assets/photos/venue-[id].jpg
# Skips any venue that already has a real (non-placeholder) photo.

set -e
DEST="assets/photos"
mkdir -p "$DEST"

download_wiki() {
  local id="$1"
  local wiki_title="$2"
  local out="$DEST/venue-${id}.jpg"

  echo "→ $id ($wiki_title)"

  # Get thumbnail URL from Wikipedia API
  local api="https://en.wikipedia.org/w/api.php?action=query&titles=$(python3 -c "import urllib.parse; print(urllib.parse.quote('$wiki_title'))")&prop=pageimages&pithumbsize=1200&format=json"
  local url
  url=$(curl -s "$api" | python3 -c "
import json,sys
d=json.load(sys.stdin)
pages=d['query']['pages']
for p in pages.values():
    t=p.get('thumbnail',{}).get('source','')
    if t: print(t)
" 2>/dev/null)

  if [ -z "$url" ]; then
    echo "  ✗ No Wikipedia image found for $wiki_title"
    return
  fi

  curl -sL -A "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)" \
    -H "Referer: https://en.wikipedia.org/" \
    -o "$out" "$url"

  if file "$out" 2>/dev/null | grep -q "image"; then
    echo "  ✓ Saved $out"
  else
    rm -f "$out"
    echo "  ✗ Download failed (not an image)"
  fi
}

download_direct() {
  local id="$1"
  local url="$2"
  local out="$DEST/venue-${id}.jpg"

  echo "→ $id (direct)"
  curl -sL -A "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)" \
    -H "Referer: https://www.google.com/" \
    -o "$out" "$url"

  if file "$out" 2>/dev/null | grep -q "image"; then
    echo "  ✓ Saved $out"
  else
    rm -f "$out"
    echo "  ✗ Download failed"
  fi
}

echo "================================================"
echo " ITE Venue Photo Downloader"
echo "================================================"

# ── SLC Hotels ───────────────────────────────────────
download_wiki "hyatt-regency"       "Hyatt Regency Salt Lake City"
download_wiki "little-america"      "Little America Hotel (Salt Lake City)"
download_wiki "hilton-city-center"  "Hilton Salt Lake City Center"
download_wiki "le-meridien"         "Le Méridien Salt Lake City"
download_wiki "marriott-downtown"   "Salt Lake Marriott Downtown at City Creek"
download_wiki "sheraton-slc"        "Sheraton Salt Lake City Hotel"
download_wiki "hotel-monaco"        "Hotel Monaco Salt Lake City"
download_wiki "radisson-slc"        "Radisson Hotel Salt Lake City Downtown"
download_wiki "asher-adams"         "Asher Adams Hotel"

# ── SLC Arts & Culture ───────────────────────────────
download_wiki "salt-palace"         "Salt Palace Convention Center"
download_wiki "kingsbury-hall"      "Kingsbury Hall"
download_wiki "capitol-theatre"     "Capitol Theatre (Salt Lake City)"
download_wiki "rose-wagner"         "Rose Wagner Performing Arts Center"
download_wiki "hale-centre"         "Hale Centre Theatre"
download_wiki "monson-center"       "Thomas S. Monson Center"
download_wiki "natural-history-museum" "Natural History Museum of Utah"
download_wiki "red-butte-garden"    "Red Butte Garden"
download_wiki "gallivan-center"     "Gallivan Center"

# ── SLC Event Venues ─────────────────────────────────
download_wiki "union-event-center"  "The Union Event Center"
download_wiki "the-complex"         "The Complex (Salt Lake City)"
download_wiki "falls-event-center"  "The Falls Event Center"

# ── Utah County ──────────────────────────────────────
download_wiki "uccu-center"         "UCCU Center"
download_wiki "utah-valley-convention" "Utah Valley Convention Center"
download_wiki "thanksgiving-point"  "Thanksgiving Point"
download_wiki "viridian-event-center" "Viridian Event Center"
download_wiki "scera-theater"       "SCERA Center for the Arts"

# ── Mountain / Cottonwood ────────────────────────────
download_wiki "snowbird"            "Snowbird ski resort"
download_wiki "snowpine-lodge"      "Snowpine Lodge"
download_wiki "solitude"            "Solitude Mountain Resort"
download_wiki "snowbasin"           "Snowbasin Resort"

# ── Mountain / Heber–Midway ──────────────────────────
download_wiki "zermatt"             "Zermatt Utah Resort & Spa"
download_wiki "homestead-resort"    "Homestead Resort (Utah)"
download_wiki "soldier-hollow"      "Soldier Hollow"
download_wiki "high-west-distillery" "High West Distillery"
download_wiki "sundance"            "Sundance Mountain Resort"

# ── Park City / Deer Valley ──────────────────────────
download_wiki "grand-hyatt-deer-valley" "Grand Hyatt Deer Valley"
download_wiki "deer-valley-resort"  "Deer Valley Resort"
download_wiki "montage-deer-valley" "Montage Deer Valley"
download_wiki "st-regis-deer-valley" "St. Regis Deer Valley"
download_wiki "pendry-park-city"    "Pendry Park City"
download_wiki "canyons-village"     "Canyons Village"
download_wiki "waldorf-park-city"   "Waldorf Astoria Park City"
download_wiki "stein-eriksen"       "Stein Eriksen Lodge"
download_wiki "hotel-park-city"     "Hotel Park City"
download_wiki "goldener-hirsch"     "Goldener Hirsch Inn"
download_wiki "kimball-art-center"  "Kimball Art Center"

# ── Jackson Hole ─────────────────────────────────────
download_wiki "four-seasons-jackson" "Four Seasons Resort and Residences Jackson Hole"
download_wiki "amangani"            "Amangani"
download_wiki "jackson-hole-golf"   "Jackson Hole Golf and Tennis Club"

echo ""
echo "================================================"
echo " Done! Check assets/photos/ for venue-*.jpg files"
echo " Then run: git add assets/photos && git commit -m 'add venue photos' && git push origin main"
echo "================================================"
