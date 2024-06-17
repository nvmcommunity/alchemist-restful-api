# Library Structure

## Overview

Folder structure of the library is as follows:

```
library
├── src
│   ├── Common (1)
│   │   ├── Exceptions (1.1)
│   │   │   ├── <Exception> AlchemistRestfulApiException.php (1.1.1)
│   │   ├── Helpers (1.2)
│   │   │   ├── <Static Class> Arrays.php (1.2.1)
│   │   │   ├── <Static Class> Strings.php (1.2.2)
│   │   │   ├── <Static Class> Numerics.php (1.2.3)
│   │   ├── Integrations (1.3)
│   │   │   ├── Adapters (1.3.1)
│   │   │   │   ├── <Class> AlchemistAdapter.php (1.3.1.1)
│   │   │   ├── <Abstract Class> AlchemistQueryable.php (1.3.2)
│   │   │   ├── <Abstract Class> StatefulAlchemistQueryable (1.3.3)
│   │   ├── Notification (1.4)
│   │   │   ├── <Class> ErrorBag.php (1.4.1)
│   │   │   ├── <Class> CompoundErrors.php (1.4.2)
│   ├── ModuleA (2)
│   │   ├── Handlers (2.1)
│   │   │   ├── <Class> ModuleA.php (2.1.1)
│   │   ├── Notifications (2.2)
│   │   │   ├── <Class> ModuleA[ErrorBag].php (2.2.1)
│   │   ├── Objects (2.3)
│   │   ├── <Trait> ModuleA[able].php (2.4)
│   ├── ModuleB
│   │   ├── ...
│   ├── ModuleC
│   │   ├── ...
│   ├── AlchemistRestfulApi.php (3)
```

## Description

### 1. Common

#### 1.1. Exceptions

- **1.1.1. AlchemistRestfulApiException.php**
  - Custom exception class for the library.
  - Extends `Exception` class.
  - Used for throwing exceptions in the library.

#### 1.2. Helpers

- **1.2.1. Arrays.php**
  - Static class for array helper functions.
  - Contains functions for array manipulation.
  - Used for array operations in the library.
- **1.2.2. Strings.php**
  - Static class for string helper functions.
  - Contains functions for string manipulation.
  - Used for string operations in the library.
- **1.2.3. Numerics.php**
  - Static class for numeric helper functions.
  - Contains functions for numeric manipulation.
  - Used for numeric operations in the library.

#### 1.3. Integrations

- **1.3.1. Adapters**
  - **1.3.1.1. AlchemistAdapter.php**
    - Adapter class for integrating with external services.
    - Custom error messages.
    - Custom which components to use.
    - Custom request parameters used.
- **1.3.2. AlchemistQueryable.php**
  - Abstract class for defining rules for querying data.
- **1.3.3. StatefulAlchemistQueryable**
  - same as `AlchemistQueryable` but non-static.

#### 1.4. Notification

- **1.4.1. ErrorBag.php**
  - Used for error handling in the library.
- **1.4.2. CompoundErrors.php**
  - Used for containing multiple errors of all components.

### 2. ModuleA

- **2.1. Handlers**
  - **2.1.1. ModuleA.php**
    - Main classes for handling ModuleA.
- **2.2. Notifications**
  - **2.2.1. ModuleA[ErrorBag].php**
      - ErrorBag class for ModuleA.
      - Contains error messages for ModuleA.
- **2.3. Objects**
    - This folder contains objects for ModuleA.
- **2.4. ModuleA[able].php**
  - An entry point for ModuleA.
  - Contains methods for init ModuleA.
  - Contains access method for ModuleA Handlers.

### 3. AlchemistRestfulApi.php
- Main class for the library.