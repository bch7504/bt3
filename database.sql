CREATE TABLE vehicles (
    id                SERIAL PRIMARY KEY,
    license_plate     TEXT NOT NULL UNIQUE,
    vehicle_type      TEXT,
    brand             TEXT,
    model             TEXT,
    color             TEXT,
    owner_name        TEXT,
    engine_number     TEXT,
    chassis_number    TEXT,
    registration_date DATE,
    inspection_expiry DATE,
    status            TEXT NOT NULL DEFAULT 'active',
    created_at        TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_vehicles_owner ON vehicles (owner_name);

INSERT INTO vehicles
  (license_plate, vehicle_type, brand, model, color, owner_name,
   engine_number, chassis_number, registration_date, inspection_expiry, status)
VALUES
  ('30A-123.45', 'Ô tô con', 'Toyota',  'Vios',      'Trắng', 'Nguyễn Văn An',   'ENG-30A12345', 'CHS-30A12345', '2021-03-15', '2026-03-14', 'active'),
  ('29B-543.21', 'Ô tô con', 'Honda',   'City',      'Đen',   'Trần Thị Bình',   'ENG-29B54321', 'CHS-29B54321', '2020-07-01', '2025-06-30', 'expired'),
  ('51F-678.90', 'Xe tải',   'Hyundai', 'Porter',    'Xanh',  'Lê Văn Cường',    'ENG-51F67890', 'CHS-51F67890', '2019-11-20', '2026-11-19', 'active'),
  ('43A-111.22', 'Ô tô con', 'Mazda',   'CX-5',      'Đỏ',    'Phạm Thị Dung',   'ENG-43A11122', 'CHS-43A11122', '2022-01-10', '2027-01-09', 'active'),
  ('59P1-234.56','Xe máy',   'Honda',   'Wave Alpha','Đen',   'Hoàng Văn Em',    'ENG-59P23456', 'CHS-59P23456', '2018-05-05', '2026-05-04', 'active'),
  ('29X1-888.99','Xe máy',   'Yamaha',  'Exciter',   'Xanh',  'Đỗ Thị Hoa',      'ENG-29X88899', 'CHS-29X88899', '2021-09-12', '2026-09-11', 'active'),
  ('30G-456.78', 'Ô tô con', 'Kia',     'Morning',   'Bạc',   'Vũ Văn Giang',    'ENG-30G45678', 'CHS-30G45678', '2020-12-01', '2025-11-30', 'expired'),
  ('61C-234.11', 'Xe khách', 'Thaco',   'County',    'Trắng', 'Bùi Văn Hùng',    'ENG-61C23411', 'CHS-61C23411', '2019-03-22', '2026-03-21', 'active'),
  ('92A-777.66', 'Xe bán tải','Ford',   'Ranger',    'Cam',   'Ngô Thị Lan',     'ENG-92A77766', 'CHS-92A77766', '2022-06-30', '2027-06-29', 'active'),
  ('36B-999.00', 'Xe máy',   'Honda',   'SH',        'Đỏ',    'Đặng Văn Minh',   'ENG-36B99900', 'CHS-36B99900', '2017-08-18', '2024-08-17', 'suspended');

INSERT INTO vehicles
  (license_plate, vehicle_type, brand, model, color, owner_name,
   engine_number, chassis_number, registration_date, inspection_expiry, status)
SELECT
  lpad((11 + i % 89)::text, 2, '0')
    || (ARRAY['A','B','C','D','F','G','H','K','L'])[1 + i % 9]
    || '-' || substr(lpad(i::text, 5, '0'), 1, 3) || '.' || substr(lpad(i::text, 5, '0'), 4, 2),
  v.vehicle_type,
  v.brand,
  v.model,
  (ARRAY['Trắng','Đen','Xanh','Đỏ','Bạc','Cam','Vàng','Xám'])[1 + i % 8],
  (ARRAY['Nguyễn','Trần','Lê','Phạm','Hoàng','Vũ','Đặng','Bùi','Đỗ','Ngô'])[1 + i % 10]
    || ' ' || (ARRAY['Văn','Thị','Đức','Minh','Ngọc','Hữu','Thanh'])[1 + i % 7]
    || ' ' || (ARRAY['An','Bình','Cường','Dung','Giang','Hoa','Hùng','Lan','Nam','Oanh','Phúc','Quân','Sơn','Tâm','Uyên','Vinh','Xuân','Yến'])[1 + i % 18],
  'ENG-' || lpad(i::text, 8, '0'),
  'CHS-' || lpad(i::text, 8, '0'),
  d.reg_date,
  d.expiry,
  CASE
    WHEN i % 25 = 0 THEN 'suspended'
    WHEN d.expiry < CURRENT_DATE THEN 'expired'
    ELSE 'active'
  END
FROM generate_series(1, 9990) AS i
CROSS JOIN LATERAL (
  SELECT (ARRAY['Toyota','Honda','Hyundai','Mazda','Kia','Ford','Yamaha','Thaco','VinFast','Suzuki'])[1 + i % 10] AS brand,
         (ARRAY['Vios','City','Porter','CX-5','Morning','Ranger','Exciter','County','VF 8','Swift'])[1 + i % 10]   AS model,
         (ARRAY['Ô tô con','Ô tô con','Xe tải','Ô tô con','Ô tô con','Xe bán tải','Xe máy','Xe khách','Ô tô con','Ô tô con'])[1 + i % 10] AS vehicle_type
) v
CROSS JOIN LATERAL (
  SELECT s.reg_date, s.reg_date + 365 * (2 + i % 4) AS expiry
  FROM (SELECT date '2015-01-01' + (i * 37) % 3650 AS reg_date) s
) d;

CREATE TABLE violations (
    id          SERIAL PRIMARY KEY,
    vehicle_id  INT NOT NULL REFERENCES vehicles(id) ON DELETE CASCADE,
    description TEXT NOT NULL,
    violated_at DATE NOT NULL DEFAULT CURRENT_DATE
);

CREATE INDEX idx_violations_date ON violations (violated_at);

INSERT INTO violations (vehicle_id, description, violated_at)
SELECT
  1 + i % 2000,
  (ARRAY['Vượt đèn đỏ','Quá tốc độ','Đỗ sai quy định','Không đội mũ bảo hiểm',
         'Vi phạm nồng độ cồn','Đi ngược chiều'])[1 + i % 6],
  CURRENT_DATE - (i * 7) % 400
FROM generate_series(0, 1999) AS i;

ALTER TABLE vehicles
    ADD COLUMN IF NOT EXISTS owner_id_no TEXT,
    ADD COLUMN IF NOT EXISTS owner_phone TEXT,
    ADD COLUMN IF NOT EXISTS owner_email TEXT;

ALTER TABLE violations
    ADD COLUMN IF NOT EXISTS status      TEXT NOT NULL DEFAULT 'unprocessed',
    ADD COLUMN IF NOT EXISTS decision_no TEXT,
    ADD COLUMN IF NOT EXISTS due_date    DATE,
    ADD COLUMN IF NOT EXISTS province    TEXT,
    ADD COLUMN IF NOT EXISTS location    TEXT;

CREATE TABLE IF NOT EXISTS notifications (
    id           SERIAL PRIMARY KEY,
    violation_id INT NOT NULL REFERENCES violations(id) ON DELETE CASCADE,
    channel      TEXT NOT NULL,
    recipient    TEXT NOT NULL,
    message      TEXT NOT NULL,
    sent_at      TIMESTAMPTZ NOT NULL DEFAULT now()
);

UPDATE vehicles SET
    owner_id_no = lpad((id * 7919)::text, 12, '0'),
    owner_phone = '09' || lpad(id::text, 8, '0'),
    owner_email = 'chuxe' || id || '@example.com'
WHERE owner_id_no IS NULL;

WITH prov(idx, name, spots) AS (VALUES
    (0, 'Hà Nội',     ARRAY['Ngã Tư Sở', 'Cầu vượt Mai Dịch', 'QL1A - Ngọc Hồi']),
    (1, 'TP.HCM',     ARRAY['Ngã tư Hàng Xanh', 'Vòng xoay An Sương', 'XL Hà Nội - Cát Lái']),
    (2, 'Đà Nẵng',    ARRAY['Cầu Rồng', 'Ngã ba Huế']),
    (3, 'Hải Phòng',  ARRAY['QL5 - Quán Toan', 'Ngã tư Lạch Tray']),
    (4, 'Cần Thơ',    ARRAY['QL91 - Cái Răng', 'Cầu Cần Thơ']),
    (5, 'Bình Dương', ARRAY['QL13 - Thuận An', 'Ngã tư 550']),
    (6, 'Đồng Nai',   ARRAY['QL51 - Long Thành', 'Ngã tư Vũng Tàu']),
    (7, 'Nghệ An',    ARRAY['QL1A - Diễn Châu', 'Ngã tư Quán Bánh'])
)
UPDATE violations v SET
    province    = p.name,
    location    = p.name || ' - ' || p.spots[1 + (v.id / 8) % array_length(p.spots, 1)],
    status      = CASE WHEN v.id % 10 < 4 THEN 'unprocessed'
                       WHEN v.id % 10 < 7 THEN 'notified'
                       ELSE 'paid' END,
    decision_no = CASE WHEN v.id % 10 >= 4 THEN 'QD-' || lpad(v.id::text, 6, '0') END,
    due_date    = CASE WHEN v.id % 10 >= 4 THEN v.violated_at + 15 END
FROM prov p
WHERE p.idx = (v.id % 11) % 8 AND v.province IS NULL;

CREATE TABLE IF NOT EXISTS admins (
    id            SERIAL PRIMARY KEY,
    username      TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id          SERIAL PRIMARY KEY,
    admin_id    INT REFERENCES admins(id) ON DELETE SET NULL,
    action      TEXT NOT NULL,
    entity_type TEXT NOT NULL,
    entity_id   INT,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_audit_logs_created ON audit_logs (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_audit_logs_admin   ON audit_logs (admin_id);

INSERT INTO admins (username, password_hash)
VALUES ('admin', '$2y$12$k6uAUZlPUv/4IzVG8RCYGOzoAGVbI7ezGlj3lx1p0p.YAy754BHWi')
ON CONFLICT (username) DO NOTHING;
