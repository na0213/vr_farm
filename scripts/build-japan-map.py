#!/usr/bin/env python3
"""日本の都道府県の地図データ(resources/data/japan-map.json)を作る。

元データ: Natural Earth「Admin 1 – States, Provinces」1:10m(パブリックドメイン)
  https://github.com/nvkelso/natural-earth-vector/blob/master/geojson/ne_10m_admin_1_states_provinces.geojson
加工: 日本の47都道府県だけを取り出し、ランベルト正角円錐図法で投影し、小さな島と遠い離島(小笠原・大東諸島)を省き、
      線を簡略化し、沖縄県は枠で囲んで左上に置く(別のはめ込み)。

使い方: python3 scripts/build-japan-map.py <ne_10m_admin_1_states_provinces.geojson> [出力先]
地図の形を変えたいときだけ実行する(出力のJSONはリポジトリに入っている)。
"""
import json
import math
import sys

SRC = sys.argv[1]
OUT = sys.argv[2] if len(sys.argv) > 2 else 'resources/data/japan-map.json'

WIDTH = 560            # 本州などの地図の幅(viewBox の単位)
MIN_AREA = 10.0        # これより小さい島は省く(viewBox の単位の面積)。県ごとの最大の島は必ず残す
TOLERANCE = 0.22       # 線の簡略化の許容(Douglas-Peucker)
OKINAWA = '沖縄県'
INSET_SCALE = 0.75     # 沖縄のはめ込みの縮尺(本州に対して)
PAD = 14               # 枠や余白


def lcc(phi1=30.0, phi2=46.0, phi0=38.0, lam0=137.0):
    r = math.radians
    p1, p2, p0, l0 = r(phi1), r(phi2), r(phi0), r(lam0)
    t = lambda p: math.tan(math.pi / 4 + p / 2)
    n = math.log(math.cos(p1) / math.cos(p2)) / math.log(t(p2) / t(p1))
    f = math.cos(p1) * t(p1) ** n / n
    rho0 = f / t(p0) ** n

    def project(lon, lat):
        rho = f / t(r(lat)) ** n
        theta = n * (r(lon) - l0)
        return rho * math.sin(theta), -(rho0 - rho * math.cos(theta))  # 画面の y は下向き

    return project


def rings_area(ring):
    return sum(x1 * y2 - x2 * y1 for (x1, y1), (x2, y2) in zip(ring, ring[1:] + ring[:1])) / 2


def centroid(ring):
    a = rings_area(ring)
    if abs(a) < 1e-9:
        return ring[0]
    cx = cy = 0.0
    for (x1, y1), (x2, y2) in zip(ring, ring[1:] + ring[:1]):
        k = x1 * y2 - x2 * y1
        cx += (x1 + x2) * k
        cy += (y1 + y2) * k
    return cx / (6 * a), cy / (6 * a)


def rdp(points, eps):
    if len(points) < 3:
        return points
    (x1, y1), (x2, y2) = points[0], points[-1]
    dx, dy = x2 - x1, y2 - y1
    norm = math.hypot(dx, dy)
    best, idx = 0.0, 0
    for i in range(1, len(points) - 1):
        px, py = points[i]
        d = abs(dy * px - dx * py + x2 * y1 - y2 * x1) / norm if norm else math.hypot(px - x1, py - y1)
        if d > best:
            best, idx = d, i
    if best > eps:
        return rdp(points[:idx + 1], eps)[:-1] + rdp(points[idx:], eps)
    return [points[0], points[-1]]


def simplify_ring(ring):
    ring = ring[:-1] if ring[0] == ring[-1] else ring
    # 閉じた輪は、いちばん遠い2点で分けてから簡略化する
    far = max(range(len(ring)), key=lambda i: math.hypot(ring[i][0] - ring[0][0], ring[i][1] - ring[0][1]))
    a = rdp(ring[:far + 1], TOLERANCE)
    b = rdp(ring[far:] + [ring[0]], TOLERANCE)
    out = a[:-1] + b[:-1]
    return out if len(out) >= 3 else ring


def fmt(v):
    s = f'{v:.1f}'
    return s[:-2] if s.endswith('.0') else s


def path_d(rings):
    parts = []
    for ring in rings:
        cmd = []
        px = py = 0.0
        for i, (x, y) in enumerate(ring):
            rx, ry = round(x, 1), round(y, 1)
            cmd.append(('M%s %s' % (fmt(rx), fmt(ry))) if i == 0 else ('l%s %s' % (fmt(rx - px), fmt(ry - py))))
            px, py = rx, ry
        parts.append(''.join(cmd) + 'z')
    return ''.join(parts)


def main():
    data = json.load(open(SRC, encoding='utf-8'))
    project = lcc()
    prefs = []
    for f in data['features']:
        p = f['properties']
        if p.get('admin') != 'Japan':
            continue
        g = f['geometry']
        polys = g['coordinates'] if g['type'] == 'MultiPolygon' else [g['coordinates']]
        name = p['name_ja']
        keep = []
        for poly in polys:
            ring = poly[0]
            lon_c = sum(x for x, _ in ring) / len(ring)
            lat_c = sum(y for _, y in ring) / len(ring)
            if name == '東京都' and lat_c < 32:  # 小笠原などの遠い離島は省く
                continue
            if name == OKINAWA and lon_c > 130:  # 大東諸島は省く(はめ込みの枠が横に広がるため)
                continue
            keep.append([(x, y) for x, y in map(lambda c: project(*c), ring)])
        prefs.append({'name': name, 'code': p['iso_3166_2'], 'rings': keep})

    # 本州などの範囲(沖縄を除く)で、横幅が WIDTH になる縮尺を決める
    xs = [x for pf in prefs if pf['name'] != OKINAWA for r in pf['rings'] for x, _ in r]
    ys = [y for pf in prefs if pf['name'] != OKINAWA for r in pf['rings'] for _, y in r]
    minx, maxx, miny, maxy = min(xs), max(xs), min(ys), max(ys)
    scale = WIDTH / (maxx - minx)

    def to_px(pt, ox=0.0, oy=0.0, k=1.0):
        return ((pt[0] - minx) * scale * k + ox, (pt[1] - miny) * scale * k + oy)

    # 沖縄は縮尺を変えて、左上の海のあたりに置く
    okinawa = next(pf for pf in prefs if pf['name'] == OKINAWA)
    oxs = [x for r in okinawa['rings'] for x, _ in r]
    oys = [y for r in okinawa['rings'] for _, y in r]
    inset_w = (max(oxs) - min(oxs)) * scale * INSET_SCALE
    inset_h = (max(oys) - min(oys)) * scale * INSET_SCALE
    frame = {'x': PAD, 'y': PAD, 'w': round(inset_w + PAD * 2, 1), 'h': round(inset_h + PAD * 2 + 12, 1)}

    out = []
    for pf in prefs:
        rings_px = []
        for ring in pf['rings']:
            if pf['name'] == OKINAWA:
                pts = [((x - min(oxs)) * scale * INSET_SCALE + PAD * 2, (y - min(oys)) * scale * INSET_SCALE + PAD * 2) for x, y in ring]
            else:
                pts = [to_px((x, y), PAD, PAD) for x, y in ring]
            rings_px.append(pts)
        # 小さな島を省く(県ごとの最大の島は残す)
        areas = [abs(rings_area(r)) for r in rings_px]
        biggest = max(range(len(rings_px)), key=lambda i: areas[i])
        kept = [simplify_ring(r) for i, r in enumerate(rings_px) if i == biggest or areas[i] >= MIN_AREA]
        main = kept[[abs(rings_area(r)) for r in kept].index(max(abs(rings_area(r)) for r in kept))]
        cx, cy = centroid(main)
        out.append({'name': pf['name'], 'code': pf['code'], 'd': path_d(kept), 'cx': round(cx, 1), 'cy': round(cy, 1)})

    width = round((maxx - minx) * scale + PAD * 2, 1)
    height = round((maxy - miny) * scale + PAD * 2, 1)
    out.sort(key=lambda p: p['code'])
    result = {
        'source': 'Natural Earth (public domain) admin-1, 1:10m. 加工: 簡略化・沖縄県を枠で別表示',
        'viewBox': [0, 0, width, height],
        'inset': {**frame, 'label': '沖縄県'},
        'prefectures': out,
    }
    with open(OUT, 'w', encoding='utf-8') as fp:
        json.dump(result, fp, ensure_ascii=False, separators=(',', ':'))
    print(f'{len(out)} prefectures, viewBox {width}x{height}, {sum(len(p["d"]) for p in out)} chars of path data -> {OUT}')


main()
