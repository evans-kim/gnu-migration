# gnu-magration
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
- 기존 사용자 로그인
- 사용자 가입
- 비밀번호 찾기
- 게시판
    - 게시판 CRUD
    - 게시판 스킨설정 (Vue Dynamic Components)
    - 게시글 CRUD
    - 답글 CRUD
    - 코멘트 CRUD
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

    composer require evanskim/gnu-magration

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

session 드라이버를 사용할 때에는 반드시 g4_member 테이블에 mb_no (auto increment primary key) 필드가 있어야 합니다.
만약에 해당 필드가 없다면 마이그레이션 파일을 추가하고 마이그레이션 하세요.
자동로그인 기능을 위해서도 아래의 마이그레이션을 추가하고 실행하세요.

    php artisan vendor:publish --tag=config    

폼필드 값을 보낼 라우트는 기존의 값과 동일합니다. 대부분의 정적 라우트 값을 그대로 차용하는 것을 원칙으로 합니다.

    <form method="post" action='http://mydoman.com/bbs/login.php'>

뷰에서 로그인 컨트롤러로 보내는 폼 인풋 name 은 반드시 'mb_id' , 'mb_password' 이어야 합니다.

    <input id="mb_id" type="text" class="form-control{{ $errors->has('mb_id') ? ' is-invalid' : '' }}" name="mb_id" value="{{ old('mb_id') }}" required autofocus>
    <input id="mb_password" type="password" class="form-control{{ $errors->has('mb_password') ? ' is-invalid' : '' }}" name="mb_password" required>