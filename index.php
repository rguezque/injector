<?php

require __DIR__.'/vendor/autoload.php';

use App\Dependency;
use App\Principal;
use Forge\Injector\Injector;

class Test {
    public function bar() {
        echo '3.141692654';
    }
}

$injector = new Injector();
$injector->add(Principal::class)->addParameter(Dependency::class);
$injector->add(Dependency::class)->addParameters(['Scott', 'Summers']);
$injector->add('pi', function() {
    return new Test;
});

$p = $injector->get(Principal::class);
$p->foo();

$pi = $injector->get('pi');
$pi->bar();

?>