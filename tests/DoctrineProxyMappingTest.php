<?php

namespace App\Tests;

use App\Dto\FooDto;
use App\Entity\Foo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\ObjectMapper\Metadata\ReflectionObjectMapperMetadataFactory;
use Symfony\Component\ObjectMapper\ObjectMapper;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DoctrineProxyMappingTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metadata);
    }

    public function test(): void
    {
        $objectMapper = new ObjectMapper(new ReflectionObjectMapperMetadataFactory(),
            new PropertyAccessor(),
        );

        $foo = new Foo();
        $foo->setName('bar');

        $fooDto = $objectMapper->map($foo, FooDto::class);
        self::assertInstanceOf(FooDto::class, $fooDto);

        $this->entityManager->persist($foo);
        $this->entityManager->flush();

        $proxy = $this->entityManager->getProxyFactory()->getProxy(Foo::class, ['id' => 1])->setName('bar');

        $fooDto = $objectMapper->map($proxy, FooDto::class);
        self::assertInstanceOf(FooDto::class, $fooDto);
    }
}
