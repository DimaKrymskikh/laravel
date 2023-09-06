<?php

namespace Database\Copy\Person;

class BaseTestUser
{
    public function __invoke(): object
    {
        return (object) [
            'login' => 'BaseTestLogin',
            'password' => '$2y$10$x/PwYKbYT4fzOlqyXXCtNOzLMDTJ.seMbl/mW1jElkWFB9QllWnd2', // BaseTestPassword0
            'email' => 'basetestlogin@example.com',
        ];
    }
}
