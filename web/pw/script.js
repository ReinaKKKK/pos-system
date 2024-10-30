// HTML文書の読み込みが完了した後に実行
document.addEventListener('DOMContentLoaded', () => {
    // 初回ロード時にデフォルトのパスワードを生成・表示
    generatePasswords();

    // ボタンがクリックされたときにパスワードを生成するイベントリスナーを追加
    document.getElementById('btn').addEventListener('click', generatePasswords);

    // カスタム長さのフィールドの有効化/無効化
    document.querySelectorAll('input[name="passLength"]').forEach((elem) => {
        elem.addEventListener('change', function() {
            const lengthCustomInput = document.getElementById('lengthCustomInput');
            if (this.value === 'custom') {
                lengthCustomInput.disabled = false;  // カスタム選択で入力フィールドを有効化
            } else {
                lengthCustomInput.disabled = true;   // その他の選択で無効化
                lengthCustomInput.value = 4; // デフォルト値
            }
        });
    });

    // カスタム生成数のフィールドの有効化/無効化
    document.querySelectorAll('input[name="passQuantity"]').forEach((elem) => {
        elem.addEventListener('change', function() {
            const otherQuantityInput = document.getElementById('quantityOtherInput');
            if (this.value === 'other') {
                otherQuantityInput.disabled = false;  // カスタム選択で入力フィールドを有効化
            } else {
                otherQuantityInput.disabled = true;   // その他の選択で無効化
                otherQuantityInput.value = 8; // デフォルト値
            }
        });
    });
});

// パスワードを生成する関数  
function generatePasswords() {
    // 選択された長さを取得
    const lengthOptions = document.getElementsByName('passLength');
    let length = 12;  // デフォルト長さ
    for (const option of lengthOptions) {
        if (option.checked) {
            if (option.value === 'custom') {
                length = parseInt(document.getElementById('lengthCustomInput').value) || 8;
            } else {
                length = parseInt(option.value);
            }
        }
    } 
    
    // 各オプションのチェック状態を取得
    const includeLowercase = document.getElementById('includeLowercase').checked;
    const includeUppercase = document.getElementById('includeUppercase').checked;
    const includeNumbers = document.getElementById('includeNumbers').checked;
    const includeHyphen = document.getElementById('includeHyphen').checked;
    const includeUnderscore = document.getElementById('includeUnderscore').checked;
    const includeSymbols = document.getElementById('includeSymbols').checked;
   
    // 文字セットを設定
    const charset = [
        ...(includeLowercase ? 'abcdefghijklmnopqrstuvwxyz'.split('') : []),
        ...(includeUppercase ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('') : []),
        ...(includeNumbers ? '0123456789'.split('') : []),
        ...(includeHyphen ? '-'.split('') : []),
        ...(includeUnderscore ? '_'.split('') : []),
        ...(includeSymbols ? document.getElementById('customSymbols').value.split('') : []),
    ];

    // 生成するパスワード数を取得
    const quantityOptions = document.getElementsByName('passQuantity');
        let passwordCount = 10;  // デフォルトは10個
        for (const option of quantityOptions) {
            if (option.checked) {
                if (option.value === 'other') {
                    passwordCount = parseInt(document.getElementById('quantityOtherInput').value) || 8;
                } else {
                    passwordCount = parseInt(option.value);
            }
        }
    }

    // パスワード生成結果を表示するためにカラムをクリア
    const passwordColumn = document.getElementById('password-column');
    passwordColumn.innerHTML = ''; // 必要に応じてここでクリア

    // パスワードを生成する関数各パスワードを生成
    for (let i = 0; i < passwordCount; i++) {
        let password = '';

        /// 必須文字（各カテゴリーから少なくとも1文字）を追加
        if (includeLowercase) {
            password += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)];
        }
        if (includeUppercase) {
            password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)];
        }
        if (includeNumbers) {
            password += '0123456789'[Math.floor(Math.random() * 10)];
        }
        if (includeHyphen) {
            password += '-';
        }
        if (includeUnderscore) {
            password += '_';
        }
        // カスタム記号を取得
        const customSymbols = document.getElementById('customSymbols').value;
        if (includeSymbols && customSymbols) {
            password += customSymbols[Math.floor(Math.random() * customSymbols.length)];
        }
        
        // 残りの文字をランダムに追加
        while (password.length < length) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }

        // パスワードをシャッフル
        password = password.split('').sort(() => Math.random() - 0.5).join('');

        // 表示用のパスワードボックスを生成
        const passwordBox = document.createElement('div');
        passwordBox.classList.add('password-box');


        // パスワード表示用の <span>
        const overflowDiv = document.querySelector('.overflow-div');
        const passwordSpan = document.createElement('span');
        passwordSpan.textContent = password;  // textContentを使用
        passwordBox.appendChild(passwordSpan);

        // コピー用ボタン
        const copyButton = document.createElement('button');
        copyButton.classList.add('copy-btn');
        copyButton.innerHTML = '📋';
        copyButton.onclick = () => copyPassword(password);  // パスワードをコピー
        passwordBox.appendChild(copyButton);

        // パスワード列に追加
        passwordColumn.appendChild(passwordBox);
        
        // スクロールのデバッグ情報をログ出力
        console.log('生成されたパスワード数:', passwordCount);
        console.log('パスワード列の高さ:', passwordColumn.scrollHeight);
        console.log('パスワード列の表示高さ:', passwordColumn.clientHeight);
    }
}

// パスワードをクリップボードにコピーする関数
function copyPassword(password) {
    navigator.clipboard.writeText(password).then(() => {
        alert('パスワードがクリップボードにコピーされました。');
    }).catch(err => {
        console.error('クリップボードへのコピーに失敗しました: ', err);
    });
}

