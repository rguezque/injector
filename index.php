<?php

require __DIR__.'/vendor/autoload.php';

use App\Dependency;
use App\Principal;
use Forge\Injector\Injector;

class Test {
    private $name;
    public function __construct(Foo $name) {
        $this->name = $name;
    }
    public function bar() {
        echo '3.141692654';
    }
    public function name() {
        $this->name->baz();
    }
}

class Foo {
    private $foo;
    public function __construct(string $foo) {
        $this->foo = $foo;
    }
    public function baz() {
        echo $this->foo;
    }
}

$injector = new Injector();
//$injector->add(Principal::class)->addParameter(Dependency::class);
//$injector->add(Dependency::class)->addParameters(['Scott', 'Summers']);
$injector->add(Foo::class, function() {
    return new Foo('Fuuaaaa');
});
$injector->add(Test::class)->addParameter(Foo::class);

//$p = $injector->get(Principal::class);
//$p->foo();

$pi = $injector->get(Test::class);
$pi->name();

?>