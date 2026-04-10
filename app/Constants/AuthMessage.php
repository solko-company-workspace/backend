<?php

namespace App\Constants;

class AuthMessage
{
    const LOGIN_SUCCESS    = '로그인 되었습니다.';
    const LOGIN_FAIL       = '아이디 또는 비밀번호가 올바르지 않습니다.';
    const PENDING_APPROVAL = '승인대기중입니다.';
    const REGISTER_SUCCESS = '회원가입이 완료되었습니다.';
    const LOGOUT_SUCCESS          = '로그아웃 되었습니다.';
    const PASSWORD_RESET_LINK_SENT = '비밀번호 재설정 링크가 발송되었습니다.';
    const PASSWORD_RESET_LINK_FAIL = '등록되지 않은 이메일입니다.';
    const PASSWORD_RESET_SUCCESS   = '비밀번호가 변경되었습니다.';
    const PASSWORD_RESET_FAIL      = '유효하지 않거나 만료된 토큰입니다.';
}
