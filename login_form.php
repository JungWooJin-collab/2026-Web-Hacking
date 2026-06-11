<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Auth Gateway - NetAdmin Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="glass-container glass-container-small" id="login-container">
        <h2 class="title-gradient">SYS_LOGIN</h2>
        <form action="actions/login.php" method="POST">
            <div class="form-group">
                <label>아이디</label>
                <input type="text" name="username" placeholder="아이디를 입력하세요" required>
            </div>
            <div class="form-group">
                <label>비밀번호</label>
                <input type="password" name="password" placeholder="비밀번호를 입력하세요" required>
            </div>
            <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">INITIATE_LOGIN</button>
        </form>
        <div class="toggle-text" onclick="toggleForm()" style="text-align: center;">계정이 없으신가요? <b>회원가입</b></div>
        <div style="text-align: center; margin-top: 15px;">
            <a href="index.php" style="color: var(--text-muted, #888); font-size: 0.9rem; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--text-muted, #888)'">&larr; [ 메인 화면으로 이동 ]</a>
        </div>
    </div>

    <div class="glass-container glass-container-small" id="register-container" style="display: none;">
        <h2 class="title-gradient">SYS_REGISTER</h2>
        <form action="actions/register.php" method="POST" id="register-form" onsubmit="return validateRegister()">
            <div class="form-group">
                <label>새로운 아이디</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="reg-username" name="username" placeholder="생성할 아이디" required style="flex: 1;">
                    <button type="button" class="btn" onclick="checkDuplicate()" style="padding: 0.5rem 1rem;">중복확인</button>
                </div>
                <small id="username-status" style="display: block; margin-top: 5px;"></small>
                <input type="hidden" id="is-username-valid" value="false">
            </div>
            <div class="form-group">
                <label>새로운 비밀번호</label>
                <input type="password" name="password" placeholder="대/소문자, 숫자, 특수문자 포함 8자 이상" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" title="비밀번호는 최소 8자 이상이어야 하며, 영문 대소문자, 숫자, 특수문자를 각각 1개 이상 포함해야 합니다.">
                <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.3rem;">비밀번호: 8자 이상, 대소문자, 숫자, 특수문자 필수</small>
            </div>
            <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 10px;">CREATE_ACCESS_KEY</button>
        </form>
        <div class="toggle-text" onclick="toggleForm()" style="text-align: center;">이미 계정이 있으신가요? <b>로그인</b></div>
        <div style="text-align: center; margin-top: 15px;">
            <a href="index.php" style="color: var(--text-muted, #888); font-size: 0.9rem; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--text-muted, #888)'">&larr; [ 메인 화면으로 이동 ]</a>
        </div>
    </div>

    <script>
        function toggleForm() {
            const login = document.getElementById('login-container');
            const register = document.getElementById('register-container');
            
            if (login.style.display === 'none') {
                login.style.display = 'block';
                login.style.animation = 'fadeIn 0.4s ease';
                register.style.display = 'none';
            } else {
                login.style.display = 'none';
                register.style.display = 'block';
                register.style.animation = 'fadeIn 0.4s ease';
            }
        }

        function checkDuplicate() {
            const usernameInput = document.getElementById('reg-username');
            const username = usernameInput.value.trim();
            const statusText = document.getElementById('username-status');
            const validFlag = document.getElementById('is-username-valid');

            if (!username) {
                statusText.textContent = '아이디를 입력해주세요.';
                statusText.style.color = 'var(--text-muted, #888)';
                return;
            }

            fetch(`actions/check_username.php?username=${encodeURIComponent(username)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        statusText.textContent = '이미 사용 중인 아이디입니다.';
                        statusText.style.color = '#ff5252'; // 에러 색상
                        validFlag.value = 'false';
                    } else {
                        statusText.textContent = '사용 가능한 아이디입니다.';
                        statusText.style.color = '#4caf50'; // 정상 색상
                        validFlag.value = 'true';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    statusText.textContent = '확인 중 오류가 발생했습니다.';
                    statusText.style.color = '#ff5252';
                });
        }

        function validateRegister() {
            const validFlag = document.getElementById('is-username-valid');
            if (validFlag.value !== 'true') {
                alert('아이디 중복 확인을 통과해야 가입이 가능합니다.');
                return false;
            }
            return true;
        }

        // 아이디 입력란이 수정되면 중복확인 상태 초기화
        document.getElementById('reg-username').addEventListener('input', function() {
            document.getElementById('is-username-valid').value = 'false';
            document.getElementById('username-status').textContent = '';
        });
    </script>
</body>
</html>
