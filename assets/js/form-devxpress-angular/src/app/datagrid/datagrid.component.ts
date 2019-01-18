import {Component, OnInit, ViewChild} from '@angular/core';
import {ApiService} from '../../services/api';
import CustomStore from 'devextreme/data/custom_store';
import {BookModel} from "../../models/book.model";
import { apiConfig } from '../../../../lib/config'
import {DxDataGridComponent} from "devextreme-angular";
import notify from 'devextreme/ui/notify';
import {BroadcastChannelApi} from '../shared/services/broadcast-channel-api';

@Component({
  selector: 'my-datagrid',
  templateUrl: './datagrid.component.html',
  styleUrls: ['./datagrid.component.scss'],
})
export class DatagridComponent implements OnInit {
  @ViewChild(DxDataGridComponent) dataGrid: DxDataGridComponent;

  protected books: Array<BookModel>
  protected booksTotal: number

  protected secondScreen: {
      window: any,
      url: string
  } = {
      window: undefined,
      url: ''
  }
  public apiConfig
  public dataSource: any = {}

  constructor(private api: ApiService, private broadcastChannel: BroadcastChannelApi) {
      this.apiConfig = apiConfig

      this.broadcastChannel.message.subscribe(message => {
          switch(message.cmd) {
              case 'book':
                  const book = this.books.find(oneBook => oneBook.id === message.book.id)
                  if (!book) {
                      notify(`no book found in store with id ${message.book.id}`, "warn", 5000)
                      return
                  }

                  for (const prop in message.book) {
                      book[prop] = message.book[prop]
                  }

                  // Take care, until you click on the parent window, data won't be refreshed in datagrid. don't know if it's because of browser behavior or DevXpress.datagrid
                  notify(`new Book received with id ${book.id}, focus the window to see the changes in datagrid.
                    Data is not saved until you edit it`, 'info', 5000);
                  this.dataGrid.instance.clearSelection()

                  break
              case 'hello':
              case 'ping':
              default:
                  notify(`data received from second screen (cmd=${message.cmd})`, "info", 5000)
                  break
          }
      })

      // @todo: the store should be moved into services folder and injected here instead of the ApiService
      this.dataSource.store = new CustomStore({
          // I use byKey method to modify the store from BroadcastChannel API
          // when i receive a new Book, then i alter the this.books (which is not the store used by the datagrid)
          // because the datagrid can spy its data using byKey, then it will be alerted of the modification
          // But don't forget to focus the window or even if the this.books is altered, the datagrid won't refresh the
          // displayed data
          byKey: (key: any | string | number) => {
              const book = this.books.find(oneBook => key === oneBook.id)

              return book
          },
          load: (loadOptions: any) => {
              let itemPerPage = this.apiConfig.itemsPerPage

              let options = {
                  params: []
              }

              // manage number of items wished by the datagrid and that the API must return (take care to configure the config/packages/api_platform.yaml:client_items_per_page key)
              if (loadOptions.take) {
                  options.params[this.apiConfig.itemsPerPageParameterName] = loadOptions.take
                  itemPerPage = loadOptions.take
              }

              // manage the pagination: ApiPlatform works with hydra system and so a page number whereas DevXpress datagrid uses a skip/take parameter, so it requires a small Math calc
              if (loadOptions.skip) {
                  options.params['page'] = loadOptions.skip > 0 ? Math.ceil(loadOptions.skip / itemPerPage) +1 : 1
              }

              if(loadOptions.sort && loadOptions.sort.length) {
                  loadOptions.sort.forEach(sort => {
                      options.params[`order[${sort.selector}]`] = sort.desc ? 'desc' : 'asc'
                  })
              }

              return this.api.get('/books', options)
                  .toPromise()
                  .then(json => {
                      // not mandatory
                      this.books = json['hydra:member']
                      this.booksTotal = json['hydra:totalItems']

                      return {
                          data: json['hydra:member'],
                          totalCount: json['hydra:totalItems']
                      }
                  })
                  .catch(error => {
                      console.log(error)
                      throw 'Data Loading Error'
                  })
          },
          update: (key: any, values: any) => {
              return this.api.put(`/books/${key.id}`, values)
                  .toPromise()
                  .then(() => {
                      console.info('Update Ok')
                  })
                  .catch(e => {
                      console.warn(e)
                      throw 'Data Update Error'
                  })
          },
          remove: (key: any) => {
              return this.api.delete(`/books/${key.id}`)
                  .toPromise()
                  .then(() => {
                      console.info('Delete Ok')
                  })
                  .catch(e => {
                      console.warn(e)
                      throw 'Data Delete Error'
                  })
          }
      })
  }

  ngOnInit(): void {
    /*
    this.api.get('/books', {params: {"order[id]": 'DESC'}}).subscribe((books) => {
      this.books = books['hydra:member']
    })
    */
  }

  debug(data: any): void {
      console.log(data)
  }

  getSelectedRow () {
      const rows = this.dataGrid.instance.getSelectedRowKeys()
      const url = `/demo/form/devxpress-angular/book/${rows[0].id}`
      const options = {
          menubar: 'false',
          toolbar: 'false',
          location: 'false',
          directories: 'false',
          personalbar : 'false',
          status: 'false',

          resizable: 'yes',
          scrollbars: 'yes',
          dependent: 'yes',
          modal: 'false',
          dialog: 'false',
          minimizable: 'false', // only if dialog=yes

          // require privilege UniversalBrowserWrite
          chrome: 'yes',
          titlebar: 'second-screen',
          alwaysRaised: 'yes',
          alwaysLowered: 'false',
          close: 'false',


          left: window.screen.width,
          top: '0',
          width: window.screen.width,
      }

    let winOptions = '';
      for (let key in options) {
          if (winOptions) {
              winOptions += ','
          }
          winOptions += `${key}=${options[key]}`
      }

      if (!this.secondScreen.window
          || this.secondScreen.url !== url) {
          this.secondScreen.window = window['open'](url, 'second-screen', winOptions)
      }

      if (this.secondScreen.window.focus) {
          this.secondScreen.window.focus()
      }
  }
}
