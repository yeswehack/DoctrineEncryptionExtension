<?php

namespace YWH\Encryptable\Mapping\Driver;

use Doctrine\DBAL\Types\Type;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\Mapping\Driver;
use Gedmo\Mapping\Driver\File;
use Symfony\Component\Yaml\Yaml as YmlParser;

/**
 * Class Yaml
 *
 * @author Romain Honel <r.honel@yeswehack.com>
 * @author Maxime Bouchard <m.bouchard@yeswehack.com>
 */
class Yaml extends File implements Driver
{
    /**
     * File extension
     * @var string
     */
    protected $_extension = '.dcm.yml';

    /**
     * List of types which are valid for encryption
     *
     * @var array
     */
    protected $validFieldTypes = array(
        'text',
    );

    /**
     * {@inheritDoc}
     */
    public function readExtendedMetadata($meta, array &$config)
    {
        $mapping = $this->_getMapping($meta->name);

        if (isset($mapping['ywh'])) {
            $classMapping = $mapping['ywh'];
            if (isset($classMapping['encryptable'])) {
                $config['usePassword'] = isset($classMapping['encryptable']['usePassword']) ? $classMapping['encryptable']['usePassword'] : false;
                $config['encryptKey'] = isset($classMapping['encryptable']['encryptKey']) ? $classMapping['encryptable']['encryptKey'] : false;
                if (isset($mapping['fields'])) {
                    foreach ($mapping['fields'] as $field => $fieldMapping) {
                        if (isset($fieldMapping['ywh']['encrypted'])) {
                            $mappingProperty = $fieldMapping['ywh']['encrypted'];
                            if (!$meta->hasField($field)) {
                                throw new InvalidMappingException("Unable to find 'encrypted field' - [{$field}] as mapped property in entity - {$meta->name}");
                            }
                            if (!$this->isValidFieldType($meta, $field)) {
                                throw new InvalidMappingException("Encrypted field - [{$field}] type is not valid and must be 'text' in entity - {$meta->name}");
                            }
                            $config['fields'][] = array(
                                'field' => $field,
                                'type' => Type::getType($mappingProperty['type']),
                            );
                        }
                        elseif (isset($fieldMapping['ywh']['encryptionKey'])) {
                            $mappingProperty = $fieldMapping['ywh']['encryptionKey'];
                            if (!$meta->hasField($field)) {
                                throw new InvalidMappingException("Unable to find 'encryption key field' - [{$field}] as mapped property in entity - {$meta->name}");
                            }
                            if (!$this->isValidFieldType($meta, $field)) {
                                throw new InvalidMappingException("Encryption key field - [{$field}] type is not valid and must be 'text' in entity - {$meta->name}");
                            }
                            $config['keyField'] = $field;
                        }
                    }
                }

                if ($config['usePassword'] && $config['encryptKey']) {
                    throw new InvalidMappingException("You cannot use both key protected by password and key encrypted in entity - {$meta->name}");
                }

                if ($config['encryptKey'] && !isset($config['keyField'])) {
                    throw new InvalidMappingException("You need to map a key field as the encryption key to use a encrypted key in entity - {$meta->name}");
                }

                if ($config['usePassword'] && !isset($config['keyField'])) {
                    throw new InvalidMappingException("You need to map a key field as the encryption key to use a key protected by password in entity - {$meta->name}");
                }
            }
        }
    }

    /**
     * Loads a mapping file with the given name and returns a map
     * from class/entity names to their corresponding elements.
     *
     * @param string $file The mapping file to load.
     *
     * @return array
     */
    protected function _loadMappingFile($file)
    {
        return YmlParser::parse(file_get_contents($file));
    }

    /**
     * Checks if $field type is valid
     *
     * @param object $meta
     * @param string $field
     *
     * @return boolean
     */
    private function isValidFieldType($meta, $field)
    {
        $mapping = $meta->getFieldMapping($field);

        return $mapping && in_array($mapping['type'], $this->validFieldTypes);
    }
}