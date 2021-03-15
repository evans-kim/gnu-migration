# gnu-migration
그누보드4 기반 웹사이트를 라라벨로 마이그레이션 하는데 필요한 도구 개발 프로젝트

그누보드4 기반 사이트를 아래와 같이 리펙토링 하는 프로젝트 입니다.

    데이터베이스 - 그누보드4
    서버 - 라라벨 5.7 RESTful API
    프론트 - Vue.js

PHP 5.6 이 더 이상 지원되지 않는 이슈를 포함하여 모던 PHP 확산을 위해
기존 레거시에서 신규 모던 PHP 로 가는 작은 디딤돌이 되면 좋겠습니다.

한 때, 너무나 편하게 접근할 수 있었기에 저 같은 컴맹도 스크립팅에 도전하게 해 준 그누보드에 진심 감사를 표합니다.
그러나 이제는 애물단지가 된...현실...

앞으로는 모던PHP에게 길을 내어 주십시오!

gnu4 R.I.P


#### TODO
- ~~기존 사용자 로그인~~
- ~~비밀번호 찾기~~ 
- ~~사용자 가입~~
- ~~게시판~~
    - ~~게시판 CRUD~~
    - ~~게시판 스킨설정 (Vue Dynamic Components)~~
    - ~~게시글 CRUD~~
    - ~~답글 CRUD~~
    - ~~코멘트 CRUD~~
    - 첨부파일 CRUD
    - 접근권한설정
- 관리자 페이지
    - 사용자관리
    - 게시판관리
    - 파일관리
    - ...
- ...

그누보드 특성상 여러가지로 커스터마이징 된 사례가 많아 통합하기 어렵겠지만
필수적이다고 생각되는 기능이 있다면 리퀘스트나 이슈 올리시면 고민해 보겠습니다.

## 사용방법

#### 설치하기
라라벨을 설치한 폴더에서 패키지를 설치합니다.
라라벨 5.5 이상은 자동으로 서비스 프로바이더를 등록됩니다.
이후 필요한 기능을 아래에서 찾아 적용하시면 됩니다.

    composer require evanskim/gnu-migration


그누보드4 데이터베이스를 사용하는데 가장 큰 문제는 date 타입 컬럼에 디폴트로 '0000-00-00 00:00:00' 설정된 점 입니다.
최근 (mysql 5.7 이상) 에서는 이를 허용하지 않으므로 디폴트를 null 로 변경해 주어야 합니다. 아니면 
이를 위해서는 마이그레이션을 실행하기 전에 config/database.php 의 strict 값을 false 로 변경하고 실행해야 에러가 무시되고
date 타입의 디폴트 값이 null 로 변경됩니다. 
(MYSQL에서 해당 옵션을 변경하면 바꾸지 않아도 됩니다.)

기존의 컬럼을 변경해야 하기 위해선 아래의 패키지가 설치되어 있어야 합니다.

    composer require doctrine/dbal
    
config/database.php

    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4', // 경우에 따라 utf8 으로 설정
        'collation' => 'utf8mb4_unicode_ci', // 경우에 따라 utf8_unicode_ci 으로 설정
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => false, // true 값을 false 로 변경!
        'engine' => null,
    ],

session 드라이버를 사용할 때에는 반드시 g4_member 테이블에 mb_no (auto increment primary key) 필드가 있어야 합니다.
만약에 해당 필드가 없다면 마이그레이션 파일을 추가하고 마이그레이션 하세요.
자동로그인 기능을 위해서도 아래의 마이그레이션을 추가하고 실행하세요.    

    php artisan vendor:publish --provider="EvansKim\GnuMigration\GnuMigrationServiceProvider"
    php artisan migrate

#### 1.사용자 인증
기존의 g4_member 테이블에 있는 사용자 정보를 그대로 이용하여 라라벨에서 로그인 할 수 있습니다.
아래의 파일과 같이 해당 파일을 수정해 주세요.

LoginController.php
    
    use EvansKim\GnuMigration\AuthenticatesGnuMembers;
    
    class LoginController extends Controller
    {
         use AuthenticatesGnuMembers; // 기존의 트레이스를 교체합니다.    


config/auth.php
인증 드라이버는 file, session 만 지원됩니다. 

    'web' => [
        'driver' => 'session',
        'provider' => 'members', // 원래값은 users 
    ],
    'providers' => [
        // 프로바이더에 새로 추가 또는 
        'members' => [
            'driver' => 'gnu'
        ],
    ]

폼필드 값을 보낼 라우트는 기존의 값과 동일합니다. 대부분의 정적 라우트 값을 그대로 차용하는 것을 원칙으로 합니다.

API

    curl -X POST "http://mydomain/bbs/login.php" 
        -d "mb_id"="test" -d "mb_password"="test" -d "remember"="false" //or true

#### 2. 비밀번호 찾기
라라벨의 비밀번호 찾기 기능도 동일하게 사용할 수 있습니다. 아래의 설정파일에서 provider를 변경해 주세요.

config/auth.php

    'passwords' => [
        'users' => [
            'provider' => 'members', // users -> members 로 바꾸어 주세요. 
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],
    
아래의 컨트롤러 트레이스를 변경해 주세요.
app\Http\Controllers\Auth\ResetPasswordController.php
    
    //use Illuminate\Foundation\Auth\ResetsPasswords;
    use EvansKim\GnuMigration\GnuResetsPasswords as ResetsPasswords;
    
    
API

    curl -X POST "http://mydomain/bbs/password_lost2.php"     -d "email"="test@test.com"
    
#### 3. 회원가입
라라벨의 인증 기능을 그대로 차용하여 확장하였기 때문에 라라벨의 이벤트와 리스너 , Telescope 에서도 라라벨 기본 인증과
동일하게 확장하여 쓸 수 있습니다.
 
API

    curl -X POST "http://gnu-migration.com/bbs/register_form_update.php"     \
        -d "mb_id"="test" \
        -d "mb_password"="Test1234" \
        -d "mb_name"="홍길동" \
        -d "mb_nick"="테스터" \
        -d "mb_email"="tester@test.com" 

#### 4. 게시판 목록

게시판은 모델과 프론트엔드가 분리된 형태로 구조화 했습니다. 하나의 게시판은 하나의 모델을 가지며 설정에 따라 다른 스킨을 입힐 수 있습니다.
프론트엔드는 모두 vue.js 로 처리되며 api 호출을 통해 ajax 로 데이터 수발신이 이루어 집니다.
보통 게시판 스킨에는 프론트와 컨트롤 모델이 모두 혼재해 있으므로 이를 적절하게 구분지어야 합니다.

    모델 - app/Boards 
    뷰 - Vue 
        ( vue-router 이용하여 List, Show, Edit, Create, Reply 페이지 라우팅)
    컨트롤러 - EvansKim\GnuMigration\Controller\Board\GnuBoardController
        (index,store,update,destroy) 

이번에 선택한 게시판 스킨의 주요 기술은 Vue Dynamic Component 입니다. 비동기 방식으로 Vue component 를 불러오는 방식인데요.
게시판에 설정된 스킨명을 따라 게시판의 전체 테마 그리고 각 기능별 주요 페이지를 별도로 커스터마이징 할 수 있습니다.
아래와 같이 Theme 컴포넌트 안에 슬롯으로 BoardList,CreateForm,UpdateForm,ReplyForm,ShowPage 등이 들어갈 수 있습니다.
    
    <board-dynamic>
        <theme>
            <board-list></board-list>
        </theme>
    </board-dynamic> 

###### 4.1. 게시판 모델

그누보드는 게시판 기능을 확장하여 다양한 커스터마이즈를 하는 구조가 많아 게시판 하나당 엘로퀀트 모델 클래스를 생성하고
이 클래스에 확장된 기능을 구현하게 됩니다. 아래의 명령을 통해 스캐폴딩을 생성하세요. app/Boards 디렉토리 아래에 생성됩니다.

    php artisan make:g4board test //게시판명

###### 4.2 게시판 프론트 에셋 컴파일링

프로젝트 루트에 있는 webpack.mix.js 파일을 열어 아래의 내용을 추가합니다.
    
    mix .js('resources/vendor/gnu/js/gnu.js', 'public/js')
        .webpackConfig({
            output: {           
                chunkFilename: 'js/lazy/[name].js'
            }
        });
        // 라라벨에 기본적으로 세팅 되어 있는 값을 바꾸지 마세요.
        // bootstrap.js 는 반드시 필요합니다. (axios 및 crsf 설정 필요) 

컴파일링을 위해 필요한 노드 패키지를 설치합니다.

    npm install vue-router @babel/plugin-syntax-dynamic-import

public/js 으로 해당 스크립트를 컴파일링 합니다.

    npm run dev
    
기존의 라우트를 그대로 활용하기 때문에 아래와 같이 접근할 수 있습니다.

http://gnu-migration.com/bbs/board.php?bo_table=test

![Image of Example](https://evans-kim.github.io/gnu-migration/shots/shot01.png)


