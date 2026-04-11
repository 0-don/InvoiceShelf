import { client } from '../client'
import { API } from '../endpoints'
import type {
  AiConfig,
  AiDriversResponse,
  AiTestPayload,
  AiTestResponse,
  CompanyAiConfig,
} from '@/scripts/types/ai-config'

export const aiService = {
  // Driver catalog — same shape across admin, company, installer contexts.
  async getDrivers(): Promise<AiDriversResponse> {
    const { data } = await client.get(API.AI_DRIVERS)
    return data
  },

  // --- Global (admin) ---

  async getGlobalConfig(): Promise<AiConfig> {
    const { data } = await client.get(API.AI_CONFIG)
    return data
  },

  async saveGlobalConfig(payload: AiConfig): Promise<{ success?: string; error?: string }> {
    const { data } = await client.post(API.AI_CONFIG, payload)
    return data
  },

  async testGlobalConnection(payload: AiTestPayload): Promise<AiTestResponse> {
    const { data } = await client.post(API.AI_TEST, payload)
    return data
  },

  // --- Per-company ---

  async getCompanyConfig(): Promise<CompanyAiConfig> {
    const { data } = await client.get(API.COMPANY_AI_CONFIG)
    return data
  },

  async saveCompanyConfig(payload: CompanyAiConfig): Promise<{ success?: boolean; error?: string }> {
    const { data } = await client.post(API.COMPANY_AI_CONFIG, payload)
    return data
  },

  async testCompanyConnection(payload: AiTestPayload): Promise<AiTestResponse> {
    const { data } = await client.post(API.COMPANY_AI_TEST, payload)
    return data
  },
}
