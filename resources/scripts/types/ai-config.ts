export interface AiSuggestedModel {
  value: string
  label: string
}

export interface AiDriverConfigField {
  key: string
  type: 'text' | 'select'
  label: string
  default?: string
  options?: Array<{ label: string; value: string }>
  visible_when?: Record<string, string>
}

export interface AiDriverOption {
  value: string
  label: string
  website: string
  default_base_url: string
  supported_roles: string[]
  suggested_models: AiSuggestedModel[]
  config_fields: AiDriverConfigField[]
}

export interface AiDriversResponse {
  ai_drivers: AiDriverOption[]
}

export interface AiConfig {
  ai_enabled: 'YES' | 'NO'
  ai_driver: string
  ai_api_key: string
  ai_base_url: string
  ai_chat_enabled: 'YES' | 'NO'
  ai_chat_model: string
  ai_text_generation_enabled: 'YES' | 'NO'
  ai_text_generation_model: string
}

export interface CompanyAiConfig extends AiConfig {
  use_custom_ai_config: 'YES' | 'NO'
}

export interface AiTestPayload {
  ai_driver: string
  ai_api_key?: string
  ai_base_url?: string
}

export interface AiTestResponse {
  success?: boolean
  error?: string
  message?: string
  details?: Record<string, unknown>
}
