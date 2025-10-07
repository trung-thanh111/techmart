/**
 * Unit tests for variant helpers
 */

import { describe, it, expect } from 'vitest'
import {
  cartesianProduct,
  generateCodeFromIds,
  generateDefaultSku,
  validateCombinationLimit,
  generateAttributeDataPreview,
  isVariantModified,
  generateUniqueVariantCode
} from './variantHelpers'
import type { VariantGroup, ProductVariant } from './variantHelpers'

describe('Variant Helpers', () => {
  describe('cartesianProduct', () => {
    it('should generate all combinations from variant groups', () => {
      const groups: VariantGroup[] = [
        {
          catalogueId: 1,
          catalogueName: 'Màu sắc',
          attributes: [
            { id: 1, name: 'Đỏ' },
            { id: 2, name: 'Xanh' }
          ]
        },
        {
          catalogueId: 2,
          catalogueName: 'Dung lượng',
          attributes: [
            { id: 3, name: '128GB' },
            { id: 4, name: '256GB' }
          ]
        }
      ]

      const result = cartesianProduct(groups)
      
      expect(result).toHaveLength(4)
      expect(result[0].attributes).toEqual([1, 3])
      expect(result[0].attributeData).toEqual({
        'Màu sắc': { id: 1, name: 'Đỏ' },
        'Dung lượng': { id: 3, name: '128GB' }
      })
    })

    it('should return empty array for empty groups', () => {
      const result = cartesianProduct([])
      expect(result).toEqual([])
    })
  })

  describe('generateCodeFromIds', () => {
    it('should generate code from attribute IDs', () => {
      expect(generateCodeFromIds([1, 3, 7])).toBe('1_3_7')
      expect(generateCodeFromIds([7, 1, 3])).toBe('1_3_7') // Should sort
    })
  })

  describe('generateDefaultSku', () => {
    it('should generate SKU from product SKU and code', () => {
      expect(generateDefaultSku('PROD-001', '1_3_7')).toBe('PROD-001-1_3_7')
    })
  })

  describe('validateCombinationLimit', () => {
    it('should validate within limit', () => {
      const groups: VariantGroup[] = [
        {
          catalogueId: 1,
          catalogueName: 'Màu sắc',
          attributes: [{ id: 1, name: 'Đỏ' }, { id: 2, name: 'Xanh' }]
        }
      ]

      const result = validateCombinationLimit(groups, 10)
      expect(result.isValid).toBe(true)
      expect(result.totalCombinations).toBe(2)
    })

    it('should reject when over limit', () => {
      const groups: VariantGroup[] = [
        {
          catalogueId: 1,
          catalogueName: 'Màu sắc',
          attributes: Array.from({ length: 20 }, (_, i) => ({ id: i + 1, name: `Color ${i + 1}` }))
        }
      ]

      const result = validateCombinationLimit(groups, 10)
      expect(result.isValid).toBe(false)
      expect(result.totalCombinations).toBe(20)
      expect(result.message).toContain('vượt quá giới hạn')
    })
  })

  describe('generateAttributeDataPreview', () => {
    it('should generate preview text', () => {
      const attributeData = {
        'Màu sắc': { id: 1, name: 'Đỏ' },
        'Dung lượng': { id: 4, name: '128GB' }
      }

      const result = generateAttributeDataPreview(attributeData)
      expect(result).toBe('Màu sắc: Đỏ, Dung lượng: 128GB')
    })
  })

  describe('isVariantModified', () => {
    it('should detect modifications', () => {
      const original: ProductVariant = {
        album: [],
        code: '1_3',
        stock: 0,
        sku: 'PROD-001-1_3',
        price: null,
        barcode: null,
        attributeData: {},
        attributes: [1, 3],
        publish: 2
      }

      const modified: ProductVariant = {
        ...original,
        price: 1000000,
        stock: 50
      }

      expect(isVariantModified(modified, original)).toBe(true)
    })
  })

  describe('generateUniqueVariantCode', () => {
    it('should generate unique code', () => {
      const existingCodes = ['1_3', '1_4', '2_3']
      const result = generateUniqueVariantCode(existingCodes, '1_3')
      expect(result).toBe('1_3_1')
    })

    it('should return original code if unique', () => {
      const existingCodes = ['1_3', '1_4']
      const result = generateUniqueVariantCode(existingCodes, '2_3')
      expect(result).toBe('2_3')
    })
  })
})
