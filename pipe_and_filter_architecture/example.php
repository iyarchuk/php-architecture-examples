<?php

// Autoload classes (in a real application, you would use Composer's autoloader)
spl_autoload_register(function ($class) {
    $prefix = 'PipeAndFilterArchitecture\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Import classes
use PipeAndFilterArchitecture\Filter\TextLowerCaseFilter;
use PipeAndFilterArchitecture\Filter\TextUpperCaseFilter;
use PipeAndFilterArchitecture\Filter\TextReverseFilter;
use PipeAndFilterArchitecture\Filter\WordCountFilter;
use PipeAndFilterArchitecture\Filter\RemoveSpecialCharsFilter;
use PipeAndFilterArchitecture\Pipe\SimplePipe;
use PipeAndFilterArchitecture\Pipeline;

// Example 1: Using individual filters with pipes
echo "Example 1: Using individual filters with pipes\n";
$input = "Hello, World! This is a Pipe and Filter example.";
echo "Original text: $input\n";

// Create filters
$lowerCaseFilter = new TextLowerCaseFilter();
$upperCaseFilter = new TextUpperCaseFilter();
$reverseFilter = new TextReverseFilter();
$removeSpecialCharsFilter = new RemoveSpecialCharsFilter();

// Create pipes and connect filters
$lowerCasePipe = new SimplePipe();
$lowerCasePipe->registerFilter($lowerCaseFilter);

$upperCasePipe = new SimplePipe();
$upperCasePipe->registerFilter($upperCaseFilter);

$reversePipe = new SimplePipe();
$reversePipe->registerFilter($reverseFilter);

$removeSpecialCharsPipe = new SimplePipe();
$removeSpecialCharsPipe->registerFilter($removeSpecialCharsFilter);

// Process data through individual pipes
$lowerCaseResult = $lowerCasePipe->pass($input);
echo "After lowercase filter: $lowerCaseResult\n";

$upperCaseResult = $upperCasePipe->pass($input);
echo "After uppercase filter: $upperCaseResult\n";

$reverseResult = $reversePipe->pass($input);
echo "After reverse filter: $reverseResult\n";

$removeSpecialCharsResult = $removeSpecialCharsPipe->pass($input);
echo "After remove special chars filter: $removeSpecialCharsResult\n";

// Example 2: Using a pipeline to chain multiple filters
echo "\nExample 2: Using a pipeline to chain multiple filters\n";

// Create a pipeline with multiple filters
$pipeline1 = new Pipeline();
$pipeline1->addFilter($lowerCaseFilter)
          ->addFilter($removeSpecialCharsFilter)
          ->addFilter($reverseFilter);

// Process data through the pipeline
$result1 = $pipeline1->process($input);
echo "Result of pipeline (lowercase -> remove special chars -> reverse): $result1\n";

// Create another pipeline with a different order of filters
$pipeline2 = Pipeline::create([
    $removeSpecialCharsFilter,
    $upperCaseFilter
]);

// Process data through the second pipeline
$result2 = $pipeline2->process($input);
echo "Result of pipeline (remove special chars -> uppercase): $result2\n";

// Example 3: Using the WordCountFilter
echo "\nExample 3: Using the WordCountFilter\n";
$wordCountFilter = new WordCountFilter();
$wordCountPipe = new SimplePipe();
$wordCountPipe->registerFilter($wordCountFilter);

$wordCountResult = $wordCountPipe->pass($input);
echo "Original text: {$wordCountResult['text']}\n";
echo "Word count: {$wordCountResult['word_count']}\n";

// Example 4: Creating a more complex pipeline
echo "\nExample 4: Creating a more complex pipeline\n";

// First, remove special characters and convert to lowercase
$preprocessPipeline = Pipeline::create([
    $removeSpecialCharsFilter,
    $lowerCaseFilter
]);

$preprocessedText = $preprocessPipeline->process($input);
echo "Preprocessed text: $preprocessedText\n";

// Then, count words in the preprocessed text
$wordCountResult = $wordCountFilter->process($preprocessedText);
echo "Word count after preprocessing: {$wordCountResult['word_count']}\n";

// Example 5: Demonstrating error handling
echo "\nExample 5: Demonstrating error handling\n";
try {
    $nonStringInput = 123;
    $result = $lowerCaseFilter->process($nonStringInput);
} catch (\InvalidArgumentException $e) {
    echo "Error caught: " . $e->getMessage() . "\n";
}

try {
    $emptyPipe = new SimplePipe();
    $result = $emptyPipe->pass($input);
} catch (\RuntimeException $e) {
    echo "Error caught: " . $e->getMessage() . "\n";
}