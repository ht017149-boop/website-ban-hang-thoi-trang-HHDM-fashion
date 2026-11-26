USE website_ban_hang_thoi_trang;

ALTER TABLE orders
  ADD COLUMN cancel_request_status VARCHAR(20) NOT NULL DEFAULT 'none',
  ADD COLUMN cancel_reason VARCHAR(500) NULL,
  ADD COLUMN cancel_requested_at DATETIME NULL,
  ADD COLUMN cancel_reviewed_at DATETIME NULL,
  ADD COLUMN cancel_admin_note VARCHAR(500) NULL,
  ADD COLUMN refund_status VARCHAR(20) NOT NULL DEFAULT 'none',
  ADD COLUMN refund_note VARCHAR(500) NULL,
  ADD COLUMN cod_collected_at DATETIME NULL,
  ADD COLUMN archived_at DATETIME NULL;
