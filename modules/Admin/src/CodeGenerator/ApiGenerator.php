use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use Laminas\Code\Generator\MethodGenerator;

class ApiGenerator {
    public function createEntity(string $entityName, array $fields) {
        $class = new ClassGenerator();
        $class->setName($entityName)
              ->setNamespaceName("Application\Entity")
              ->addUse('Doctrine\ORM\Mapping as ORM');

        // Adiciona campos
        foreach ($fields as $field) {
            $property = new PropertyGenerator($field['name']);
            $property->addFlag(PropertyGenerator::FLAG_PRIVATE)
                     ->setDocBlock("@ORM\Column(type=\"{$field['type']}\")\n");
            
            // Gera getters/setters
            $class->addMethod(
                'get' . ucfirst($field['name']),
                [],
                MethodGenerator::FLAG_PUBLIC,
                'return $this->' . $field['name'] . ';'
            );
            
            $class->addMethod(
                'set' . ucfirst($field['name']),
                ['value'],
                MethodGenerator::FLAG_PUBLIC,
                '$this->' . $field['name'] . ' = $value;\nreturn $this;'
            );
            
            $class->addPropertyFromGenerator($property);
        }

        // Adiciona mapeamento Doctrine
        $class->setDocBlock(
            "@ORM\Entity\n@ORM\Table(name=\"" . strtolower($entityName) . "\")"
        );

        return "<?php\n\n" . $class->generate();
    }
}
