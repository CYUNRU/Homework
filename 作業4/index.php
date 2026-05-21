<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>郵件發送管理系統</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 20px auto; padding: 0 20px; color: #333; }
        .section { background: #f9f9f9; padding: 20px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #ddd; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"], .form-group input[type="email"], .form-group textarea, .form-group input[type="number"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #007BFF; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        button:disabled { background: #ccc; }
        .progress-box { margin-top: 20px; display: none; }
        .progress-bar-bg { background: #eee; border-radius: 4px; height: 20px; width: 100%; overflow: hidden; margin-top: 5px; }
        .progress-bar { background: #28a745; height: 100%; width: 0%; transition: width 0.3s; }
        #log { max-height: 150px; overflow-y: auto; background: #222; color: #fff; padding: 10px; font-family: monospace; font-size: 12px; margin-top: 10px; border-radius: 4px; }
    </style>
</head>
<body>

    <h2>郵件發送管理系統</h2>

    <!-- 1. 新增 Email 到資料庫 -->
    <div class="section">
        <h3>1. 新增訂閱者 Email</h3>
        <form id="addEmailForm">
            <div class="form-group">
                <input type="email" id="new_email" placeholder="請輸入 Email 位址" required>
            </div>
            <button type="submit">加入資料庫</button>
        </form>
    </div>

    <!-- 2. 郵件撰寫與發送設定 -->
    <div class="section">
        <h3>2. 撰寫郵件與發送設定</h3>
        <form id="mailForm">
            <div class="form-group">
                <label>郵件主旨</label>
                <input type="text" id="subject" value="測試電子報" required>
            </div>
            <div class="form-group">
                <label>郵件內容 (支援 HTML)</label>
                <textarea id="content" rows="5" required>感謝您訂閱我們的電子報！</textarea>
            </div>
            
            <hr>
            
            <div class="form-group">
                <label>發送模式</label>
                <input type="radio" name="mode" value="all" checked id="mode_all"> <label style="display:inline;" for="mode_all">全部發送</label> &nbsp;
                <input type="radio" name="mode" value="random" id="mode_rand"> <label style="display:inline;" for="mode_rand">隨機發送幾筆</label>
                <input type="number" id="limit_num" placeholder="筆數" style="width: 80px; margin-left: 5px;" min="1" disabled>
            </div>

            <div class="form-group">
                <label>寄送間隔時間</label>
                <input type="radio" name="interval_type" value="fixed" checked id="int_fixed"> <label style="display:inline;" for="int_fixed">固定間隔</label> &nbsp;
                <input type="radio" name="interval_type" value="random" id="int_rand"> <label style="display:inline;" for="int_rand">隨機間隔</label>
                <div style="margin-top: 5px;">
                    秒數設定: <input type="number" id="sec_min" value="2" style="width: 60px;" min="0"> 
                    <span id="range_max" style="display:none;"> 到 <input type="number" id="sec_max" value="5" style="width: 60px;" min="0"></span> 秒
                </div>
            </div>

            <button type="button" id="startSendBtn">開始發送郵件</button>
        </form>

        <!-- 進度顯示區域 -->
        <div class="progress-box" id="progressBox">
            <div>發送進度: <span id="progressText">0%</span> (<span id="progressCount">0/0</span>)</div>
            <div>目前發送時間: <span id="currentTime">-</span></div>
            <div class="progress-bar-bg">
                <div class="progress-bar" id="progressBar"></div>
            </div>
            <div id="log"></div>
        </div>
    </div>

    <script>
        // 切換隨機筆數輸入框的啟用狀態
        document.querySelectorAll('input[name="mode"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                document.getElementById('limit_num').disabled = (e.target.value === 'all');
            });
        });

        // 切換隨機秒數輸入框的顯示狀態
        document.querySelectorAll('input[name="interval_type"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                document.getElementById('range_max').style.display = (e.target.value === 'random') ? 'inline' : 'none';
            });
        });

        // 非同步延時函式 (用來做發信間隔)
        const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

        // 輔助函式：將 FormData 轉換為 URL 編碼字串
        function toParams(obj) {
            return new URLSearchParams(obj).toString();
        }

        // 處理 1: 新增 Email
        document.getElementById('addEmailForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('new_email').value;
            
            const response = await fetch('api.php?action=add_email', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: toParams({ email })
            });
            const data = await response.json();
            alert(data.message);
            if(data.success) document.getElementById('new_email').value = '';
        });

        // 處理 2: 批次發信核心邏輯
        document.getElementById('startSendBtn').addEventListener('click', async () => {
            const btn = document.getElementById('startSendBtn');
            const subject = document.getElementById('subject').value;
            const content = document.getElementById('content').value;
            const mode = document.querySelector('input[name="mode"]:checked').value;
            const limit = document.getElementById('limit_num').value;
            const intervalType = document.querySelector('input[name="interval_type"]:checked').value;
            const secMin = parseFloat(document.getElementById('sec_min').value) || 0;
            const secMax = parseFloat(document.getElementById('sec_max').value) || 0;

            if(!subject || !content) return alert('請填寫郵件主旨與內容');

            btn.disabled = true;
            
            // 初始化進度條與日誌
            const progressBox = document.getElementById('progressBox');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const progressCount = document.getElementById('progressCount');
            const currentTimeSpan = document.getElementById('currentTime');
            const log = document.getElementById('log');
            
            progressBox.style.display = 'block';
            log.innerHTML = '正在初始化發送名單...<br>';
            progressBar.style.width = '0%';
            progressText.innerText = '0%';

            // 步驟 A: 向後端索取發送名單
            try {
                const resList = await fetch('api.php?action=get_targets', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: toParams({ mode, limit })
                });
                const listData = await resList.json();
                
                if(!listData.success || !listData.emails.length) {
                    log.innerHTML += '❌ 找不到可發送的 Email 名單。<br>';
                    btn.disabled = false;
                    return;
                }

                const emails = listData.emails;
                const total = emails.length;
                log.innerHTML += `總共取得 ${total} 筆發送目標，開始發送...<br>`;

                // 步驟 B: 迴圈發送
                for(let i = 0; i < total; i++) {
                    const currentEmail = emails[i];
                    
                    // 顯示目前發送時間
                    const now = new Date();
                    currentTimeSpan.innerText = now.toLocaleTimeString();

                    log.innerHTML += `[${now.toLocaleTimeString()}] 正在發送至 ${currentEmail}... `;
                    log.scrollTop = log.scrollHeight;

                    // 呼叫後端 API 寄信
                    const resSend = await fetch('api.php?action=send_mail', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: toParams({ email: currentEmail, subject, content })
                    });
                    const sendResult = await resSend.json();

                    if(sendResult.success) {
                        log.innerHTML += `<span style="color:#28a745;">成功</span><br>`;
                    } else {
                        log.innerHTML += `<span style="color:#dc3545;">失敗 (${sendResult.message})</span><br>`;
                    }

                    // 更新進度條
                    const processed = i + 1;
                    const percent = Math.round((processed / total) * 100);
                    progressBar.style.width = `${percent}%`;
                    progressText.innerText = `${percent}%`;
                    progressCount.innerText = `${processed}/${total}`;
                    log.scrollTop = log.scrollHeight;

                    // 如果不是最後一筆，執行間隔等待
                    if (processed < total) {
                        let waitSec = secMin;
                        if (intervalType === 'random') {
                            // 在 min 到 max 之間隨機取一個秒數（支援小數點）
                            waitSec = Math.random() * (secMax - secMin) + secMin;
                        }
                        log.innerHTML += `⏳ 等待 ${waitSec.toFixed(1)} 秒後發送下一筆...<br>`;
                        log.scrollTop = log.scrollHeight;
                        await sleep(waitSec * 1000);
                    }
                }

                log.innerHTML += `✨ 任務完成！所有郵件發送完畢。<br>`;
                log.scrollTop = log.scrollHeight;

            } catch (err) {
                log.innerHTML += `❌ 發生系統錯誤: ${err.message}<br>`;
            } finally {
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>