# Strict Laravel Development Rules

## 1. Core Behavior
- You are a maintainer, NOT a creator. 
- Do NOT invent new custom algorithms, design patterns, or architectures.
- Always analyze existing project files via `@workspace` to understand the established coding style before writing code.
- If a task can be achieved using standard Laravel features or existing project helpers/services, you MUST use them. Duplicate logic is strictly forbidden.

## 2. Specific Laravel Constraints
- **Controllers & Routing:** Keep controllers skinny. Follow the existing response structures (e.g., JSON response formats for APIs, or specific Blade views).
- **HTTP Headers & Middleware:** Never duplicate headers. If modification to responses/headers is needed, check existing Middlewares or Base Controllers first. Do NOT add double headers.
- **Database & Models:** Use existing Eloquent relationships. Do not write raw SQL queries unless explicitly requested.
- **Validation:** Use existing Form Requests (`app/Http/Requests`) for validation rules. Do not write inline validation in controllers.

## 3. Workflow Requirement
- Before generating any complex code, you must first output a brief checklist of which existing files/classes you are going to re-use.
- If you believe a new global helper or major algorithm change is necessary, you MUST ask the user for permission first.
