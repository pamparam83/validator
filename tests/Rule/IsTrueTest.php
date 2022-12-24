<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\IsTrue;
use Yiisoft\Validator\Rule\IsTrueHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class IsTrueTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new IsTrue();
        $this->assertSame('isTrue', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new IsTrue(),
                [
                    'trueValue' => '1',
                    'strict' => false,
                    'messageWithType' => [
                        'template' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => '1',
                        ],
                    ],
                    'messageWithValue' => [
                        'template' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => '1',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new IsTrue(trueValue: true, strict: true),
                [
                    'trueValue' => true,
                    'strict' => true,
                    'messageWithType' => [
                        'template' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => 'true',
                        ],
                    ],
                    'messageWithValue' => [
                        'template' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => 'true',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new IsTrue(
                    trueValue: 'YES',
                    strict: true,
                    messageWithType: 'Custom message with type.',
                    messageWithValue: 'Custom message with value.',
                    skipOnEmpty: true,
                    skipOnError: true
                ),
                [
                    'trueValue' => 'YES',
                    'strict' => true,
                    'messageWithType' => [
                        'template' => 'Custom message with type.',
                        'parameters' => [
                            'true' => 'YES',
                        ],
                    ],
                    'messageWithValue' => [
                        'template' => 'Custom message with value.',
                        'parameters' => [
                            'true' => 'YES',
                        ],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            [true, [new IsTrue()]],
            ['1', [new IsTrue()]],
            ['1', [new IsTrue(strict: true)]],
            [true, [new IsTrue(trueValue: true, strict: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            ['5', [new IsTrue()], ['' => ['The value must be "1".']]],
            [null, [new IsTrue()], ['' => ['The value must be "1".']]],
            [[], [new IsTrue()], ['' => ['The value must be "1".']]],
            [true, [new IsTrue(strict: true)], ['' => ['The value must be "1".']]],
            ['1', [new IsTrue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],
            [[], [new IsTrue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],

            [false, [new IsTrue()], ['' => ['The value must be "1".']]],
            ['0', [new IsTrue()], ['' => ['The value must be "1".']]],
            ['0', [new IsTrue(strict: true)], ['' => ['The value must be "1".']]],
            [false, [new IsTrue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],

            'custom message with value' => [
                5,
                [new IsTrue(messageWithValue: 'Custom error.')],
                ['' => ['Custom error.']],
            ],
            'custom message with value with parameters' => [
                5,
                [new IsTrue(messageWithValue: 'Attribute - {attribute}, true - {true}, value - {value}.')],
                ['' => ['Attribute - , true - 1, value - 5.']],
            ],
            'custom message with value with parameters, custom true value, strict' => [
                5,
                [
                    new IsTrue(
                        trueValue: true,
                        strict: true,
                        messageWithValue: 'Attribute - {attribute}, true - {true}, value - {value}.',
                    ),
                ],
                ['' => ['Attribute - , true - true, value - 5.']],
            ],
            'custom message with value with parameters, attribute set' => [
                ['data' => 5],
                [
                    'data' => new IsTrue(messageWithValue: 'Attribute - {attribute}, true - {true}, value - {value}.'),
                ],
                ['data' => ['Attribute - data, true - 1, value - 5.']],
            ],
            'custom message with value, null' => [
                null,
                [new IsTrue(messageWithValue: 'Attribute - {attribute}, true - {true}, value - {value}.'),],
                ['' => ['Attribute - , true - 1, value - null.']],
            ],
            'custom message with type' => [
                [],
                [new IsTrue(messageWithType: 'Custom error.')],
                ['' => ['Custom error.']],
            ],
            'custom message with type with parameters' => [
                [],
                [
                    new IsTrue(messageWithType: 'Attribute - {attribute}, true - {true}, type - {type}.'),
                ],
                ['' => ['Attribute - , true - 1, type - array.']],
            ],
            'custom message with type with parameters, custom true and false values, strict' => [
                [],
                [
                    new IsTrue(
                        trueValue: true,
                        strict: true,
                        messageWithType: 'Attribute - {attribute}, true - {true}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - , true - true, type - array.']],
            ],
            'custom message with type with parameters, attribute set' => [
                ['data' => []],
                [
                    'data' => new IsTrue(messageWithType: 'Attribute - {attribute}, true - {true}, type - {type}.'),
                ],
                ['data' => ['Attribute - data, true - 1, type - array.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new IsTrue(), new IsTrue(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new IsTrue(), new IsTrue(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [IsTrue::class, IsTrueHandler::class];
    }
}
