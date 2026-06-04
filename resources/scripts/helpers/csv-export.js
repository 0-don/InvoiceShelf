import http from '@/scripts/http/index.js'

export async function downloadCsvExport(url, params = {}) {
  const response = await http.get(url, {
    params,
    responseType: 'blob',
  })

  const contentDisposition = response.headers['content-disposition']
  let filename = 'export.csv'

  if (contentDisposition) {
    const match = contentDisposition.match(/filename="?([^";]+)"?/)

    if (match) {
      filename = match[1]
    }
  }

  const blobUrl = window.URL.createObjectURL(
    new Blob([response.data], { type: 'text/csv;charset=utf-8' }),
  )
  const link = document.createElement('a')
  link.href = blobUrl
  link.setAttribute('download', filename)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  window.URL.revokeObjectURL(blobUrl)
}
