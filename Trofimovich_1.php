<?php
class DependencyException extends Exception
{
}
class NotSimilarNameException extends DependencyException
{
}
class NotHaveDependencyInName extends DependencyException
{
}
class NotJointDependencyInKey extends DependencyException
{
}
class ItselfDependency extends DependencyException
{
}
class CycleDependencyException extends DependencyException
{
}
class zadanie_1
{
    public function getAll(array $packages, string $packageName): array
    {
        $this->validate($packages);
        $ArrayPackages = $this->ArrayOfDependencies($packages, $packageName);
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($ArrayPackages));
        $simpleArray = iterator_to_array($iterator, false);
        $reverseArray = array_reverse($simpleArray);
        $finalDependencies = array_unique($reverseArray);
        return $finalDependencies;
        return $ArrayPackages;
    }
    public function validate(array $packages): void
    {
        foreach ($packages as $key => $value) {
            if ($key !== $value['name']) {
                throw new NotSimilarNameException("$value[name] is not identically $key");
            }
        }
        foreach ($packages as $key => $value) {
            if (array_key_exists("dependencies", $value) === false) {
                throw new NotHaveDependencyInName("$key not have dependencies");
            }
        }
        foreach ($packages as $key => $value) {
            foreach ($value['dependencies'] as $keyDependencies => $valueDependencies) {
                if (!array_key_exists($valueDependencies, $packages)) {
                    throw new NotJointDependencyInKey("$valueDependencies is not member array package $key");
                }
            }
        }
        $this->check($packages, []);
    }
    private function check(array $packages, array $usedDependencies): void
    {
        foreach ($packages as $key => $package) {
            $dependencies = $package['dependencies'];
            if (!empty($dependencies)) {
                if (in_array($package['name'], $dependencies)) {
                    throw new ItselfDependency('One of packages has dependency with itself');
                }
                $usedDependencies[] = $package['name'];
                foreach ($dependencies as $dependency) {
                    if (in_array($dependency, $usedDependencies)) {
                        throw new CycleDependencyException('Cycle dependency');
                    }
                    $this->check($packages[$dependency], $usedDependencies);
                }
            }
        }
    }
    private function ArrayOfDependencies(array $packages, string $packageName): array
    {
        $fullArrayOfAllNeededPackages = [$packageName];
        foreach ($packages[$packageName]['dependencies'] as $key => $dependencie) {
            if (count($dependencie) !== 0) {
                $fullArrayOfAllNeededPackages[] = $this->ArrayOfDependencies($packages, $dependencie);
            }
        }
        return $fullArrayOfAllNeededPackages;
    }

}
$PACKAGES = [
    'A' => [
        'name' => 'A',
        'dependencies' => ['B', 'C'],
    ],
    'B' => [
        'name' => 'B',
        'dependencies' => [],
    ],
    'C' => [
        'name' => 'C',
        'dependencies' => ['B', 'D'],
    ],
    'D' => [
        'name' => 'D',
        'dependencies' => [],
    ]
];
try {
    $entity = new zadanie_1();
    var_dump($entity->getAll($PACKAGES, 'A'));
} catch (DependencyException $e) {
    echo $e->getMessage();
}