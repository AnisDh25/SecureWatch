<?php

namespace App\Form;

use App\Entity\AlertRule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlertRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Rule Name',
                'attr' => [
                    'class' => 'bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white w-full focus:outline-none focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'e.g., High CPU Usage Alert'
                ]
            ])
            ->add('condition', ChoiceType::class, [
                'label' => 'Trigger Condition',
                'choices' => [
                    'High Severity Events' => 'severity_high',
                    'Critical Severity Events' => 'severity_critical',
                    'Failed Login Attempts' => 'failed_login',
                    'CPU Usage > 80%' => 'cpu_high',
                    'Disk Space > 90%' => 'disk_low',
                    'Memory Usage > 85%' => 'memory_high',
                    'Network Anomaly' => 'network_anomaly',
                ],
                'attr' => [
                    'class' => 'bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white w-full focus:outline-none focus:ring-2 focus:ring-blue-500'
                ]
            ])
            ->add('threshold', IntegerType::class, [
                'label' => 'Threshold (if applicable)',
                'required' => false,
                'attr' => [
                    'class' => 'bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white w-full focus:outline-none focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'e.g., 5 for failed login attempts'
                ]
            ])
            ->add('action', TextType::class, [
                'label' => 'Action Description',
                'attr' => [
                    'class' => 'bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white w-full focus:outline-none focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'e.g., Send email notification to admin'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save Rule',
                'attr' => [
                    'class' => 'bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 w-full'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AlertRule::class,
        ]);
    }
}
