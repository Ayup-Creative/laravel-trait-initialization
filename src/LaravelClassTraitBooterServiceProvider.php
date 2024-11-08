<?php

namespace Ayup\LaravelClassTraitBooter;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;

class LaravelClassTraitBooterServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Initialized traits.
     *
     * @var array
     */
    private array $initialized = [];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Tap into the application resolving hook.
        $this->app->resolving(function ($object) {
            // If the item being resolved is not considered an object, then we
            // don't want to look for traits.
            if (!is_object($object)) {
                return;
            }

            // If we're dealing with an object, then we can look for the use
            // of traits and begin initialisation.
            $this->executeTraitInitializationMethods($object);
        });
    }

    /**
     * Execute initialization methods for traits used in an object
     *
     * @param object $object
     * @return void
     */
    protected function executeTraitInitializationMethods(object $object): void
    {
        try {
            // Reflect on the object being resolved and create a
            // ReflectionClass instance.
            $reflection = new ReflectionClass($object);

            // Find and return an array of traits in use by the
            // object being resolved.
            $traits = $this->getUsedTraits($reflection);

            foreach ($traits as $trait => $reflection) {
                // The method name that we'll look for in the object being
                // resolved.
                $methodName = 'initialize' . class_basename($trait);

                // This is just a flag that we'll use to keep track of any
                // traits that we've already initialised.
                $traitKey = $trait . '::' . $methodName;

                // If we haven't already initialised the trait we're looking
                // at, and the method we would expect to find exists, we
                // can go ahead and call it.
                if (
                    !in_array($traitKey, $this->initialized) &&
                    method_exists($object, $methodName)
                ) {
                    // Add the trait key to our tracking property so we
                    // don't get stuck in recursion.
                    $this->initialized[] = $traitKey;

                    // Call the method using the container, this is so that
                    // we can use dependency injection in our traits.
                    app()->call([$object, $methodName]);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Trait initialization error: ' . $e->getMessage());
        }
    }

    /**
     * Recursively get all used traits including those from parent classes
     *
     * @param ReflectionClass $reflection
     * @return array
     */
    protected function getUsedTraits(ReflectionClass $reflection): array
    {
        // Extract traits used by the reflected object.
        $traits = $reflection->getTraits();

        // Recursively collect traits from parent classes
        $parentReflection = $reflection->getParentClass();
        if ($parentReflection) {
            $traits = array_merge($traits, $this->getUsedTraits($parentReflection));
        }

        return $traits;
    }
}
