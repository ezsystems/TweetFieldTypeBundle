<?php
/**
 * File containing the FormMapper class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\TweetFieldTypeBundle\eZ\Publish\FieldType\Tweet;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\TweetFieldTypeBundle\Form\TweetValueTransformer;
use EzSystems\TweetFieldTypeBundle\Form\StringToArrayTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use eZ\Publish\API\Repository\FieldTypeService;

class FormMapper implements FieldDefinitionFormMapperInterface
{
    /**
     * @param FormInterface $fieldDefinitionForm
     * @param FieldDefinitionData $data
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
            // Creating from FormBuilder as we need to add a DataTransformer.
                $fieldDefinitionForm->getConfig()->getFormFactory()->createBuilder()
                    ->create('authorList', TextType::class, [
                        'required' => false,
                        'property_path' => 'validatorConfiguration[TweetValueValidator][authorList]',
                        'translation_domain' => 'eztweet_fieldtype',
                        'label' => 'field_definition.eztweet.authorList'
                    ])
                    ->addModelTransformer(new StringToArrayTransformer())
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }
}
