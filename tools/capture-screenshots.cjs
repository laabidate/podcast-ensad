const { chromium } = require("playwright");
const path = require("path");

const base = process.env.SITE_BASE || "http://127.0.0.1:8020";
const out = process.env.SCREENSHOT_DIR || path.join(process.cwd(), "docs", "screenshots");
const edgePath = process.env.EDGE_PATH || "C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe";

async function capture() {
  const browser = await chromium.launch({ headless: true, executablePath: edgePath });
  const page = await browser.newPage({ viewport: { width: 1440, height: 1100 }, deviceScaleFactor: 1 });
  const shots = [
    ["index.html", "home.png"],
    ["episodes.html", "episodes.png"],
    ["podcasts.html", "podcasts.png"],
    ["episode-11.html", "episode-detail.png"],
    ["about.html", "about.png"],
  ];

  for (const [route, file] of shots) {
    await page.goto(`${base}/${route}`, { waitUntil: "networkidle" });
    await page.screenshot({ path: path.join(out, file), fullPage: false });
  }

  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto(`${base}/index.html`, { waitUntil: "networkidle" });
  await page.screenshot({ path: path.join(out, "mobile-home.png"), fullPage: false });
  await browser.close();
}

capture().catch((error) => {
  console.error(error);
  process.exit(1);
});
