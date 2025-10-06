# Injector
 Sencillo contenedor de dependencias.

## Agregar dependencias

El contenedor acepta como dependencias una función anónima, un método estático o una clase. Mediante el método `Injector::add` de proporciona un nombre de la dependencia y a continución la defnición de la dependencia. En el caso de que la dependencia sea una clase y no se define un nombre se tomará el propio nombre de la clase, en todos los demás casos es obligatorio definir un nombre único.

```php
use rguezque\Injector\Injector;

$injector = new Injector;

// En este caso no se define explicitamente un nombre para la dependencia
// y por default se tomará el propio nombre de la clase
$injector->add(Foo::class); 

// Se define un nombre explicito para la dependencia
$injector->add('baz_dep', Bazz::class);

// La dependencia se espera que sea un método estático, 
// pero de no serlo se creara una instancia y se ejecutará el método
$injector->add('goo', [Goo::class, 'gooAction']);

// La dependencia es una función anónima
$injector->add('my_function', function() {
    echo 'Hola mundo';
});
```

## Agregar argumentos

Para definir argumentos a ser inyectados utiliza `Injector::addArgument` o `Injector::addArguments`; el primero recibe un solo argumento y el segundo recibe un `array` con el listado de argumentos a inyectar.

Si alguno de los argumentos es a su vez una clase, recursivamente se creará la instancia de esta y será inyectada al constructor de la dependencia. En el caso de funciones anónimas y métodos estáticos, los argumentos son enviados al momento de recuperar las dependencias (Ver [Recuperar dependencias](#recuperar-dependencias)).

>[!IMPORTANT]
>Cabe mencionar que toda clase que sea definida como argumento de otra clase debe estar agregada también al contenedor como una dependencia.

```php
use rguezque\Injector\Injector;
use PDO;

$injector = new Injector;

// Se definen varios argumentos en un array
$injector->add(PDO::class)->addArguments(['mysql:host=localhost;port=3306;dbname=test;charset=utf8', 'fake_usr', 'fake_pwd']);

// Se agrega un solo argumento
$injector->add(Users::class)->addArgument(PDO::class);

```

En el ejemplo anterior la clase `User` requiere la inyección de una instancia de `PDO`, a su vez la clase `PDO` se agregó al contenedor de dependencias con sus argumentos definidos. No importa el orden de definición entre `User` y `PDO` pues solo se ejecuta la inyección de dependencias al llamar alguna.

## Recuperar dependencias

Las dependencias se recuperan con el método `Injector::get`, el cual recibe el nombre de la dependencia requerida. En caso de no existir en el contenedor arrojará una excepción `DependencyNotFoundException`. Las dependencias definidas como una clase solo retornarán la instancia de dicha clase con sus respectivas dependencias inyectadas de haber sido definidas así (Ver [Agregar argumentos](#agregar-argumentos)).

Las dependencias definidas como funciones anónimas o métodos estáticos devolverán directamente el resultado o acción que se haya definido. Adicionalmente se pueden enviar argumentos al momento de recuperar las dependencias.

```php
use rguezque\Injector\Injector;

$injector = new Injector;
$injector->add('foo', Foo::class);
$injector->add('suma', fn(int $a, int $b) => $a + $b;);
$injector->add('mult', fn(int $a, int $b) => $a * $b;)->addArguments([15, 3]);
$injector->add('goo', [Goo::class, 'myAction']);

// Devuelve la instancia de la clase
$foo = $injector->get(Foo::class);

// Devuelve un resultado de una función
$suma = $injector->get('suma', [23, 76]);

// Devolverá: 45
$suma = $injector->get('mult');

// Tambien devuelve un resultado o una acción pero de un método estático
$injector->get('goo');
```

>[!NOTE]
>Para saber si una dependencia existe usa el método `Injector::has`, el cual recibe como argumento el nombre de la dependencia buscada. Devolverá `true` si existe o  `false` en caso contrario.

>[!TIP]
>También se puede llamar a los métodos del contenedor de forma estática a través del _facade_ `Container`. Ejem.: `Container::add('foo', Foo::class)`.