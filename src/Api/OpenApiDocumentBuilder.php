<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Api;

use Kavalhub\FormGenerator\Api\Interface\FormApiDescribable;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

final class OpenApiDocumentBuilder
{
    /**
     * @param list<array{key: string, actions: list<string>, form: ElementInterface, valuesSchema?: array<string, mixed>}> $forms
     */
    public function __construct(
        private readonly string $title,
        private readonly string $endpoint,
        private readonly array $forms,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $schemas = [
            'ApiRequest' => [
                'type' => 'object',
                'required' => ['form', 'action'],
                'properties' => [
                    'form' => ['type' => 'string'],
                    'action' => ['type' => 'string'],
                    'target' => ['type' => 'string'],
                    'values' => ['type' => 'object', 'additionalProperties' => true],
                ],
            ],
            'ApiResponse' => [
                'type' => 'object',
                'properties' => [
                    'valid' => ['type' => 'boolean'],
                    'message' => ['type' => 'string'],
                    'fields' => ['type' => 'object', 'additionalProperties' => true],
                    'data' => ['type' => 'object', 'additionalProperties' => true],
                    'result' => ['type' => 'object', 'additionalProperties' => true],
                ],
            ],
        ];

        $examples = [];
        foreach ($this->forms as $item) {
            $schemaName = ucfirst($item['key']) . 'Values';
            $schemas[$schemaName] = $item['valuesSchema'] ?? FormJsonSchemaExporter::export($item['form']);
            foreach ($item['actions'] as $action) {
                $examples[] = [
                    'form' => $item['key'],
                    'action' => $action,
                    'values' => (object)[],
                ];
            }
        }

        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => $this->title,
                'version' => '1.0.0',
                'description' => 'JSON API form-generator demo. Спецификация values генерируется из дерева Form.',
            ],
            'paths' => [
                $this->endpoint => [
                    'post' => [
                        'summary' => 'Операция с формой',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/ApiRequest'],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Ответ формы',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/ApiResponse'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'get' => [
                        'summary' => 'OpenAPI спецификация',
                        'responses' => [
                            '200' => ['description' => 'Эта спецификация'],
                        ],
                    ],
                ],
            ],
            'components' => [
                'schemas' => $schemas,
                'examples' => $examples,
            ],
        ];
    }

    public static function actionsFromForm(ElementInterface $form, string $fallbackKey): array
    {
        if ($form instanceof FormApiDescribable) {
            return [
                'key' => $form->getApiKey(),
                'actions' => $form->getApiActions(),
            ];
        }

        return ['key' => $fallbackKey, 'actions' => ['submit']];
    }
}
