#!/usr/bin/env python3
"""Deploy files to remaxpartners.com via FTP with retries.

IMPORTANT: index.html is ALWAYS redeployed with every run, even if you
only pass other files. This guarantees the homepage is rewritten after
every page edit (site owner requirement).

Uploads use a temp-file + rename swap so the live file is never partial,
keeping the site stable during uploads.
"""
import ftplib
import os
import socket
import sys
import time

HOST = "remaxpartners.com"
USER = "remax@remaxpartners.com"
PASS = "remax2285$$"
LOCAL_DIR = os.path.dirname(os.path.abspath(__file__))
MAX_ATTEMPTS = 6
DEPLOYABLE_EXTS = (".html", ".css", ".js", ".json", ".php")


def upload_file(filename):
    local_path = os.path.join(LOCAL_DIR, filename)
    if not os.path.isfile(local_path):
        print(f"SKIP (not a file): {filename}")
        return False
    size = os.path.getsize(local_path)
    tmp_name = filename + ".tmp_upload"
    for attempt in range(1, MAX_ATTEMPTS + 1):
        try:
            print(f"[{filename}] attempt {attempt} (size {size} bytes)...", flush=True)
            ftp = ftplib.FTP()
            ftp.connect(HOST, 21, timeout=60)
            ftp.login(USER, PASS)
            ftp.set_pasv(True)
            ftp.sock.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
            with open(local_path, "rb") as f:
                ftp.storbinary(f"STOR {tmp_name}", f, blocksize=8192)
            # atomic-ish swap so the live file is never partial
            try:
                ftp.delete(filename)
            except Exception:
                pass
            ftp.rename(tmp_name, filename)
            ftp.quit()
            print(f"[{filename}] uploaded successfully", flush=True)
            return True
        except Exception as e:
            print(f"[{filename}] attempt {attempt} failed: {type(e).__name__}: {e}", flush=True)
            time.sleep(5)
    print(f"[{filename}] FAILED after {MAX_ATTEMPTS} attempts")
    return False


def main():
    if len(sys.argv) > 1:
        files = list(sys.argv[1:])
    else:
        files = [f for f in os.listdir(LOCAL_DIR)
                 if os.path.isfile(os.path.join(LOCAL_DIR, f))
                 and f.endswith(DEPLOYABLE_EXTS)]
    # ALWAYS include index.html in every deployment (auto-rewrite rule)
    if "index.html" not in files:
        files.append("index.html")
    results = {f: upload_file(f) for f in files}
    failed = [f for f, ok in results.items() if not ok]
    if failed:
        print("FAILED FILES:", ", ".join(failed))
        sys.exit(1)
    print("All files deployed successfully (index.html auto-included).")


if __name__ == "__main__":
    main()
