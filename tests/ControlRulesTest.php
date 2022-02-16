<?php

use PHPUnit\Framework\TestCase;
use Cocoon\Control\Validator;

class ControlRulesTest extends TestCase
{
    public function testControlAlpha()
    {
        $alpha = new validator();
        $alpha->data(['nom' => 'henri4']);
        $v = $alpha->validate(['nom' => 'alpha']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('nom'), 'Le champ nom doit être une chaîne de caractère.');
    }

    public function testControlAlphaNum()
    {
        $alpha = new validator();
        $alpha->data(['nom' => 'hhh456_-']);
        $v = $alpha->validate(['nom' => 'al_num']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('nom'), 'Le champ nom doit être une chaîne de cararactère et de nombre.');
    }

    public function testControlAlphaNumDash()
    {
        $alpha = new validator();
        $alpha->data(['nom' => '//hhh456_-']);
        $v = $alpha->validate(['nom' => 'al_num_dash']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('nom'), 'Le nom doit être une chaîne de carartère ou de nombre, les tirets et underscores sont permis.');
    }

    public function testControlInt()
    {
        $alpha = new validator();
        $alpha->data(['age' => 11,5]);
        $v = $alpha->validate(['age' => 'int']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('age'), 'Le age doit être un nombre entier.');
    }

    public function testControlEmail()
    {
        $alpha = new validator();
        $alpha->data(['mail' => 'fonzy@fr']);
        $v = $alpha->validate(['mail' => 'email']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('mail'), 'Le champ mail doit être une adresse email valide.');
    }

    public function testControlRequired()
    {
        $alpha = new validator();
        $alpha->data(['nom' => '']);
        $v = $alpha->validate(['nom' => 'required']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('nom'), 'Le champ nom est obligatoire.');
    }

    public function testControlUrl()
    {
        $alpha = new validator();
        $alpha->data(['link' => 'monsite.com']);
        $v = $alpha->validate(['link' => 'url']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('link'), 'Le champ link doit être un URL valide.');
    }

    public function testControlNumMin()
    {
        $alpha = new validator();
        $alpha->data(['nombre' => 8]);
        $v = $alpha->validate(['nombre' => 'num_min:10']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('nombre'), 'Le champ nombre doit être supérieur ou egal à 10.');
    }

    public function testControlNumMax()
    {
        $alpha = new validator();
        $alpha->data(['nombre' => 18]);
        $v = $alpha->validate(['nombre' => 'num_max:10']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('nombre'), 'Le champ nombre doit être inférieur ou egal à 10.');
    }

    public function testControlMax()
    {
        $alpha = new validator();
        $alpha->data(['string' => 'rrrrrrrr']);
        $v = $alpha->validate(['string' => 'max:4']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('string'), 'Le champ string doit avoir 4 caractères maximum.');
    }

    public function testControlMin()
    {
        $alpha = new validator();
        $alpha->data(['string' => 'rrrjjj']);
        $v = $alpha->validate(['string' => 'min:10']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('string'), 'Le champ string doit avoir 10 caractères minimum.');
    }

    public function testControlSame()
    {
        $alpha = new validator();
        $alpha->data(['string' => 'minimum', 'string_confirm' => 'maximum']);
        $v = $alpha->validate(['string_confirm' => 'same:string']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('string_confirm'), 'Le champ string_confirm doit correspondre à string.');
    }

    public function testControlArray()
    {
        $alpha = new validator();
        $alpha->data(['string' => 'maximum']);
        $v = $alpha->validate(['string' => 'array']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('string'), 'string doit être de type array(tableau).');
    }

    public function testControlRegex()
    {
        $alpha = new validator();
        $alpha->data(['string' => 'maximum44']);
        $v = $alpha->validate(['string' => 'regex:/^[a-z]$/']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('string'), 'Le string n\'est pas au bon format.');
    }

    public function testControlBetween()
    {
        $alpha = new validator();
        $alpha->data(['string' => 'maxi']);
        $v = $alpha->validate(['string' => 'between:6,12']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('string'), 'Le string doit être compris entre 6 et 12 cararactères.');
    }

    public function testControlBetweenNum()
    {
        $alpha = new validator();
        $alpha->data(['num' => 4]);
        $v = $alpha->validate(['num' => 'between_num:6,12']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('num'), 'Le num doit être compris entre 6 et 12.');
    }

    public function testControlTrue()
    {
        $alpha = new validator();
        $alpha->data(['val' => false]);
        $v = $alpha->validate(['val' => 'true']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('val'), 'vous devez cocher le champ val.');
    }

    public function testControlBool()
    {
        $alpha = new validator();
        $alpha->data(['val' => 'un']);
        $v = $alpha->validate(['val' => 'bool']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('val'), 'Le val doit être de type boolean.');
    }

    public function testControlIp()
    {
        $alpha = new validator();
        $alpha->data(['val' => '198.168.0']);
        $v = $alpha->validate(['val' => 'ip']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('val'), 'Le val doit être une adresse ip valide.');
    }

    public function testControlNum()
    {
        $alpha = new validator();
        $alpha->data(['val' => 'aa']);
        $v = $alpha->validate(['val' => 'num']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('val'), 'Le champ val doit être un nombre.');
    }

    public function testControlDate()
    {
        $alpha = new validator();
        $alpha->data(['val' => '2018']);
        $v = $alpha->validate(['val' => 'date']);
        $this->assertTrue($v->fails());
        $this->assertEquals($v->errors()->get('val'), 'Le val doit être une date valide.');
    }

    
}