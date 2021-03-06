<?php

namespace Enhavo\Bundle\SearchBundle\Metadata;

use Enhavo\Bundle\SearchBundle\EnhavoSearchBundle;
use Enhavo\Bundle\SearchBundle\Metadata\MetadataFactory;
use Enhavo\Bundle\SearchBundle\Metadata\PropertyNode;
use Enhavo\Bundle\SearchBundle\Mock\ModelMock;

/**
 * MetadataFactory.php
 *
 * @since 23/06/16
 * @author gseidel
 */
class MetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $kernelMock = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')->getMock();

        $kernelMock->method('getBundles')->willReturn([
            new EnhavoSearchBundle($kernelMock)
        ]);

        $collectorMock = $this->getMockBuilder('Enhavo\Bundle\SearchBundle\Metadata\MetadataCollector')
            ->disableOriginalConstructor()
            ->getMock();

        $collectorMock->expects($this->once())->method('getConfigurations')->willReturn([
            'Enhavo\Bundle\SearchBundle\Mock\ModelMock' => [
                'type' => 'Model',
                'properties' => [
                    'name' => [
                        0 => [
                            'Type' => [
                                'option1' => 'value1',
                                'option2' => 'value2',
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $factory = new MetadataFactory($kernelMock, $collectorMock);
        $metadata = $factory->create(new ModelMock());

        $this->assertInstanceOf('Enhavo\Bundle\SearchBundle\Metadata\Metadata', $metadata);

        $this->assertEquals('EnhavoSearchBundle', $metadata->getBundleName());
        $this->assertEquals('Enhavo\Bundle\SearchBundle\Mock\ModelMock', $metadata->getClassName());

        $properties = $metadata->getProperties();
        $this->assertCount(1, $properties);
        $this->assertInstanceOf('Enhavo\Bundle\SearchBundle\Metadata\PropertyNode', $properties[0]);

        /** @var PropertyNode $property */
        $property = $properties[0];
        $this->assertEquals('name', $property->getProperty());
        $this->assertEquals('Type', $property->getType());
        $this->assertCount(2, $property->getOptions());
    }
}