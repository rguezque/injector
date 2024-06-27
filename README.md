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

## Agregar parámetros

Solo se permite agregar parámetros cuando la dependencia es una clase. Si alguno de los parámetros es a su vez una clase, recursivamente se crerá la instancia de esta y será inyectada al constructor de la dependencia. En el caso de funciones anónimas y métodos estáticos, los parámetros son enviados al momento de recuperar las dependencias (Ver [Recuperar dependencias](#recuperar-dependencias)).

[!IMPORTANT]
Cabe mencionar que toda clase que sea definida como parámetro de otra clase debe estar agregada también al contenedor como una dependencia.

```php
use rguezque\Injector\Injector;
use PDO;

$injector = new Injector;

// Se definen varios parámetros en un array
$injector->add(PDO::class)->addParameters([/*...*/]);

// Se agregaun solo parámetro
$injector->add(Users::class)->addParameter(PDO::class);

```

En el ejemplo anterior la clase `User` requiere la inyección de una instancia de `PDO`, a su vez la clase `PDO` se agrego al contenedor de dependencias. No importa el orden de definición entre `User` y `PDO` pues solo se ejecuta la inyección de dependencias al llamar alguna.

## Recuperar dependencias

Las dependencias se recuperan con el método `Injector::get`, el cual recibe el nombre de la dependencia requerida. En caso de no existir en el contenedor arrojará una excepción `DependencyNotFoundException`. Las dependencias definidas como una clase solo retornarán la instancia de dicha clase con sus respectivas dependencias inyectadas de haber sido definidas así (Ver [Agregar parámetros](#agregar-parámetros)).

Las dependencias definidas como funciones anónimas o métodos estáticos devolverán directamente el resultado o acción que se haya definido. Adicionalmente se pueden enviar parámetros al momento de recuperar las dependencias de este tipo.

```php
use rguezque\Injector\Injector;

$injector = new Injector;
$injector->add('foo', Foo::class);
$injector->add('suma', function(int $a, int $b) {
    return $a + $b;
});
$injector->add('goo', [Goo::class, 'myAction']);

// Devuelve la instancia de la clase
$foo = $injector->get(Foo::class);

// Devuelve un resultado de una función
$suma = $injector->get('suma', [23, 76]);

// Tambien devuelve un resultado o una acción pero de un método estático
$injector->get('goo);
```

[!NOTE]
Para saber si una dependencia existe usa el método `Injector::has`, el cual recibe como argumento el nombre de la dependencia buscada. Devolverá `true` si existe o  `false` en caso contrario.