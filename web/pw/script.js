// 定数を定義
const DEFAULT_PASSWORD_COUNT = 10; // デフォルトのパスワード数
const DEFAULT_OTHER_QUANTITY = 8;   // カスタム入力のデフォルト値
const DEFAULT_LENGTH = 12;           // デフォルトのパスワード長
const LOWERCASE_CHARS = 'abcdefghijklmnopqrstuvwxyz';
const UPPERCASE_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
const NUMERIC_CHARS = '0123456789';
const HYPHEN_CHAR = '-';
const UNDERSCORE_CHAR = '_';

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
                otherQuantityInput.value = DEFAULT_OTHER_QUANTITY; // デフォルト値を定数に設定
            }
        });
    });
});

// パスワードを生成する関数
function generatePasswords() {
    const lengthOptions = document.getElementsByName('passLength');
    let length = DEFAULT_LENGTH;  // デフォルト長さを定数に設定

    // 選択された長さを取得
    for (const option of lengthOptions) {
        if (option.checked) {
            if (option.value === 'custom') {
                length = parseInt(document.getElementById('lengthCustomInput').value) || DEFAULT_LENGTH;
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
        ...(includeLowercase ? LOWERCASE_CHARS.split('') : []),
        ...(includeUppercase ? UPPERCASE_CHARS.split('') : []),
        ...(includeNumbers ? NUMERIC_CHARS.split('') : []),
        ...(includeHyphen ? HYPHEN_CHAR.split('') : []),
        ...(includeUnderscore ? UNDERSCORE_CHAR.split('') : []),
        ...(includeSymbols ? document.getElementById('customSymbols').value.split('') : []),
    ];

    // パスワード数の設定
    const quantityOptions = document.getElementsByName('passQuantity');
    let passwordCount = DEFAULT_PASSWORD_COUNT; // デフォルトは定数に設定

    for (const option of quantityOptions) {
        if (option.checked) {
            if (option.value === 'other') {
                // 'quantityOtherInput'の値を取得し、デフォルトは定数に設定
                passwordCount = parseInt(document.getElementById('quantityOtherInput').value) || DEFAULT_OTHER_QUANTITY;
            } else {
                // 他のオプションが選択された場合、その値を使用
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

        // 必須文字（各カテゴリーから少なくとも1文字）を追加
        if (includeLowercase) {
            password += LOWERCASE_CHARS[Math.floor(Math.random() * LOWERCASE_CHARS.length)];
        }
        if (includeUppercase) {
            password += UPPERCASE_CHARS[Math.floor(Math.random() * UPPERCASE_CHARS.length)];
        }
        if (includeNumbers) {
            password += NUMERIC_CHARS[Math.floor(Math.random() * NUMERIC_CHARS.length)];
        }
        if (includeHyphen) {
            password += HYPHEN_CHAR;
        }
        if (includeUnderscore) {
            password += UNDERSCORE_CHAR;
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
