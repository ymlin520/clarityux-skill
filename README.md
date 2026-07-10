# ClarityUX Skill

一個可重用的 **Microsoft Clarity UX 建議分析 Skill**，重點是：

- **RWD 響應式介面**
- **好看、可商用的分析數據畫面**
- **可放到任何 HTML / WordPress / 後台頁**
- **Clarity token / project 留在後端，不暴露在前端**
- **可每日早上 8 點自動更新數據**

## 這個 skill 的用途

把像這種 WordPress 後台頁：

- `https://hostswp.com/wp-admin/admin.php?page=hostwp-clarity-recordings`

整理成一套可重用的 UX 分析中心，之後你只要換：

- project id
- token / backend bridge
- API endpoint
- site label

就可以在其他網站直接重用同一個漂亮分析介面。

---

## 特色

### 1. RWD 響應式介面
支援：
- 手機
- 平板
- 桌機

介面會自動調整：
- KPI 卡片排列
- 問題頁卡片欄數
- 趨勢圖與建議面板堆疊方式
- 文字間距與按鈕尺寸

### 2. 好看的 UX 分析數據介面
包含：
- Hero 狀態區
- KPI summary cards
- UX issue cards
- trend chart
- top problem pages
- recording spotlight
- device / source breakdown
- UX recommendation cards

### 3. 可放到任何 HTML
你可以用在：
- 一般 HTML 網站
- WordPress admin page
- WordPress 前台頁面
- PHP dashboard
- 自訂分析後台

### 4. 每日早上 8 點自動更新
repo 已內建：
- `refresh-dashboard.php`
- `api.php`
- `.github/workflows/daily-refresh.yml`

GitHub Actions 會在：
- **Asia/Taipei 每天早上 8:00**

自動刷新 cache。

---

## Repo 內容

| 檔案 | 用途 |
|---|---|
| `SKILL.md` | Hermes skill 定義 |
| `demo/index.html` | RWD 分析介面 demo |
| `templates/standalone-clarity-dashboard.html` | 可重用 HTML 模板 |
| `api.php` | 後端 JSON endpoint |
| `refresh-dashboard.php` | 每日更新資料並寫入 cache |
| `build-dashboard.php` | 產出正式 HTML |
| `dist/dashboard.html` | build 後成品 |
| `config.example.php` | 設定檔範本 |
| `templates/api-response.schema.json` | payload schema |
| `examples/payload.example.json` | 範例資料 |
| `.github/workflows/daily-refresh.yml` | 每日 8 點自動更新 |

---

## 快速使用

### A. 本機預覽 demo
```bash
cd clarityux-skill
python3 -m http.server 8788
```

打開：
- `http://localhost:8788/demo/`

### B. 產出正式 HTML
```bash
php build-dashboard.php
```

產出：
- `dist/dashboard.html`

### C. 接真實資料
在 `config.php` 填入：
- `project_id`
- `remote_json_url`
- `remote_bearer_token`
- 或 `remote_headers`

然後由 `api.php` 提供前端資料。

---

## 架構原則

### 安全做法
1. 前端 HTML 呼叫自己的 `api.php`
2. 後端保存 Clarity token / project 設定
3. 後端抓取 Clarity 資料並正規化
4. 前端只負責渲染漂亮畫面

### 不安全做法
- 把 Clarity token 寫進前端 JS
- 在公開 HTML 中直接暴露私密 key

---

## 適合的場景

- 想把 Clarity recordings 分析做成通用 skill
- 想把 UX 建議分析頁放到任何 HTML
- 想做類似 SaaS / 顧問風格的高質感儀表板
- 想保留 RWD 與每日自動更新機制

---

## GitHub

目標 repo：
- `https://github.com/ymlin520/clarityux-skill`
