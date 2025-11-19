=== Articla media offload lite for oracle cloud infrastructure ===
Contributors: articla79
Tags: oracle cloud, oci, object storage, s3, cdn media
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.0
Stable tag: 1.3.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Offload your Media Library to Oracle Cloud (OCI) via S3. Supports private and public buckets.

== Description ==
Move your WordPress media to Oracle Cloud Infrastructure (OCI) Object Storage and serve it directly from there—reducing disk usage, speeding up delivery, and keeping your uploads organized.

**Key features**
- ✅ Offload new uploads to OCI Object Storage (S3-compatible, path-style).
- 🔁 Backfill existing media in batches, with progress notice and stop button.
- 🔐 Supports private and public buckets.
- 🔗 Rewrites media URLs to load from your bucket (or your CDN in front of it).
- 🩺 One-click Health Check to validate credentials, bucket, and endpoint.
- ⚙️ Non-destructive option to keep a local copy of files.
- 🧩 Works with common media workflows and doesn’t lock you in.

**How it works (quick start)**
1) Go to **Media → OCI Offload** and enter your **Region, Namespace, Access Key, Secret Key, and Bucket**.
2) Click **Save**, then enable **Offload new uploads** (and optionally **Keep local copy**).
3) Use **Backfill** to move existing media (optional).
4) Optionally put a CDN in front of your bucket for global performance.

**Who is it for?**
- Sites that want to save disk space on the web server.
- Publishers that prefer serving media from OCI directly or behind a CDN.
- Teams needing simple, reliable S3-compatible offloading with minimal setup.

== Screenshots ==
1. Explainer: how media offloading works with OCI Object Storage.
2. Settings screen: region, namespace, keys, bucket, and offload options.

== Installation ==
1. Install and activate the plugin.
2. Go to **Media → OCI Offload** (or **Settings → OCI Offload**) and fill in **Region, Namespace, Access Key, Secret Key, Bucket**.
3. Enable **Offload new uploads** and optionally **Keep local copy**.
4. (Optional) Run **Backfill** to move existing media.
5. (Optional) Place a CDN in front of your bucket for faster delivery.

== Frequently Asked Questions ==

= Does it support private and public buckets? =
Yes. You can use either. For private buckets, ensure your credentials have permission to PUT/GET objects.

= Do I need a CDN? =
No. It works without a CDN. A CDN is optional for better global performance and caching.

= What endpoint style does it use? =
Path-style S3 endpoints for OCI Object Storage (compat layer).

= What does the Health Check do? =
It writes a small test file to your bucket and reads it back to confirm connectivity and signing.

= Can I keep media on my server as well? =
Yes. Enable **Keep local copy** to store both locally and on OCI.

= What if I stop using the plugin later? =
Your media remains in your OCI bucket. If you kept local copies, your site will continue serving them locally after deactivation. If not, re-point URLs or re-download files from the bucket.


**Customizações para o HMDCC**

- 
  s3.php : add folder uploads on uri
  $uri = '/' . rawurlencode($o['bucket']) . '/uploads/' . implode('/', array_map('rawurlencode', explode('/', $key)));

-
  core.php: add folder uploads on function artimeof_filter_attachment_url
  $b = artimeof_compute_base_url($o).'/uploads';