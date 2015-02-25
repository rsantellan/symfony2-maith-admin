<?php
namespace Maith\Common\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of mFileVideo
 *
 * @author Rodrigo Santellan
 */
class mFileVideoType extends AbstractType
{
  
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $builder
          ->add('onlinevideo')
          ;
             
  }
  
  /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Maith\Common\AdminBundle\Entity\mFile'
        ));
    }
  
  public function getName() 
  {
    return 'maith_common_adminbundle_mfilevideo';
  }
  
  
}


