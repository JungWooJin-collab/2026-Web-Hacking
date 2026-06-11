# 게시판 웹사이트 설정 가이드 (Windows Server IIS + PHP + MySQL)

## 1. 요구사항
- Windows Server (VMware 가상 머신 등)
- IIS (Internet Information Services) 활성화
- PHP 8.x (IIS용 Non-Thread Safe 버전) 설치 및 FastCGI 연동
- MySQL 8.x 또는 MariaDB 설치

## 2. 데이터베이스 및 서버 설정
1. MySQL 클라이언트에 접속하여 `schema.sql` 파일의 내용을 실행해 데이터베이스와 테이블을 생성합니다.
   ```sql
   source C:/01.Dev/02.ptoject/board_app/schema.sql;
   ```
2. `config/db.php` 파일을 열어 `$user`와 `$pass` 변수를 실제 MySQL 접속 정보에 맞게 수정합니다.

## 3. IIS 웹 사이트 연결
1. **IIS 관리자**를 실행합니다.
2. 새 웹사이트를 추가하거나 기본 웹사이트의 [실제 경로]를 `C:\01.Dev\02.ptoject\board_app` 로 지정합니다.
3. [기본 문서] 설정에 `index.php` 가 포함되어 있는지 확인합니다.
4. **보안 권고**: `uploads/` 폴더는 사용자가 업로드한 파일이 저장되는 곳입니다. 악성 PHP 쉘 실행을 막기 위해 IIS의 [처리기 매핑(Handler Mappings)] 기능에서 해당 폴더의 PHP 실행 권한을 제거하는 것을 강력히 권장합니다. (앱 내부적으로는 파일 다운로드 시 난수화된 이름과 경로로 접근하므로 트래버설 공격 방어가 되어있습니다.)
5. `uploads/` 폴더에 대해 IUSR 및 IIS_IUSRS 계정의 [쓰기(Write)] 및 [수정(Modify)] 권한을 부여해야 파일 업로드가 정상 동작합니다.

## 4. 관리자 계정 부여 방법
앱의 회원가입 폼을 통해 임의의 계정(예: `admin_user`)으로 가입한 뒤, MySQL 데이터베이스에서 해당 유저의 `role` 값을 `admin`으로 변경합니다.
```sql
UPDATE users SET role = 'admin' WHERE username = 'admin_user';
```
해당 계정으로 다시 로그인하면 일반 사용자와 달리, 다른 모든 사용자의 게시글에 대해서도 **수정 및 삭제 버튼**이 보이며 정상적으로 작동합니다.
